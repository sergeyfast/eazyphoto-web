<?php
    use Eaze\Core\Convert;
    use Eaze\Core\DateTimeWrapper;
    use Eaze\Core\DirectoryInfo;
    use Eaze\Core\Logger;
    use Eaze\Database\ConnectionFactory;
    use Eaze\Database\SqlCommand;
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Helpers\ImageHelper;
    use Eaze\Model\BaseFactory;
    use Eaze\Site\Site;

    /**
     * AlbumUtility
     * @package    EazyPhoto
     * @subpackage Albums
     * @author     Sergeyfast
     */
    class AlbumUtility {

        /**
         * HD Path
         * shared/files/albums/key/hd
         */
        const HD = 'hd';

        /**
         * Source Path
         */
        const Source = 'source';

        /**
         * Thumbs Path
         */
        const Thumbs = 'thumbs';

        /**
         * Default Delete Days Interval
         */
        const DefaultDeleteDaysInterval = 30;

        /**
         * Default Preview Count
         */
        const DefaultPreviewCount = 6;


        /**
         * Create Dirs
         * @param Album $album
         * @return bool
         */
        public static function CreateDirs( Album $album ) {
            foreach ( [ self::HD, self::Source, self::Thumbs ] as $folderType ) {
                $path = self::GetRealPath( $album, $folderType );
                if ( !is_dir( $path ) ) {
                    $old = umask( 0 );
                    if ( !mkdir( $path, 0777, true ) ) {
                        umask( $old );
                        return false;
                    }
                    umask( $old );
                }
            }

            return true;
        }


        /**
         * Queue Album to BTSync
         *      1. Create Dirs
         *      2. Add Originals to BTSync
         *      3. Add HD to BTSync
         *      4. Update Album
         * @param Album        $album
         * @param BTSyncClient $client
         * @return bool
         */
        public static function QueueAlbum( $album, BTSyncClient $client ) {
            $result = false;
            do {
                if ( $album->statusId !== StatusUtility::InQueue ) {
                    Logger::Debug( 'Invalid Albums status for Queue' );
                    break;
                }

                if ( !self::CreateDirs( $album ) ) {
                    Logger::Warning( 'Failed to create folders for album' );
                    break;
                }

                $path   = self::GetRealPath( $album, self::Source );
                $pathHd = self::GetRealPath( $album, self::HD );
                $err    = $client->AddSyncFolder( $path, $album->roSecret );

                $hdSecret = $client->GenerateSecret();
                if ( !$hdSecret || empty( $hdSecret['secret'] ) ) {
                    Logger::Warning( 'Failed to generate readonly secret for hd' );
                    break;
                }

                if ( $err['error'] != 0 ) {
                    Logger::Warning( '%s Secret %s was not added as %s', $err['message'], $album->roSecret, $path );
                    break;
                }

                Logger::Debug( 'Successfully added %s as %s', $album->roSecret, $path );
                $album->statusId   = StatusUtility::Enabled;
                $album->modifiedAt = DateTimeWrapper::Now();
                $album->roSecretHd = $hdSecret['rosecret'];

                $errHd = $client->AddSyncFolder( $pathHd, $hdSecret['secret'] );
                // TODO Handle errHd

                if ( !AlbumFactory::Update( $album ) ) {
                    $client->RemoveSyncFolder( $path, $album->roSecret );
                    $client->RemoveSyncFolder( $pathHd, $hdSecret['secret'] );
                    Logger::Warning( 'Failed to update album %s', $album->alias );
                    break;
                }

                $result = true;
            } while ( false );

            return $result;
        }


        /**
         * Get Max Photo Id from Set
         * @param Photo[] $photos
         * @return int
         */
        public static function GetMaxPhoto( $photos ) {
            $maxId = 0;
            if ( !$photos ) {
                return $maxId;
            }

            foreach ( $photos as $photo ) {
                $t = ltrim( $photo->filename, '0' );
                $t = str_replace( '.jpg', '', $t );
                $t = Convert::ToInt( $t );

                if ( $t > $maxId ) {
                    $maxId = $t;
                }
            }

            return $maxId;
        }


        /**
         * Sync Photos with Database
         * @param Album $album
         */
        public static function SyncPhotos( Album $album ) {
            self::CreateDirs( $album );

            $sph     = SiteParamHelper::GetInstance();
            $photos  = PhotoFactory::Get( [ 'albumId' => $album->albumId ], [ BaseFactory::WithoutPages => true, BaseFactory::WithColumns => '"photoId","filename", "originalName", "statusId"' ] );
            $fileDir = DirectoryInfo::GetInstance( self::GetRealPath( $album, self::Source ) );
            $cPhotos = ArrayHelper::Collapse( $photos, 'originalName', false );
            $maxId   = self::GetMaxPhoto( $cPhotos );
            $changed = false;


            foreach ( $fileDir->GetFiles() as $f ) {
                $maxId++;

                $name = $f['filename'];
                if ( mb_strpos( mb_strtolower( $name ), 'sync' ) !== false ) {
                    continue;
                }

                if ( !empty( $cPhotos[$name] ) ) {
                    Logger::Debug( '%s already in db', $f['filename'] );
                    continue;
                }

                if ( !exif_imagetype( $f['path'] ) ) {
                    Logger::Warning( '%s is not an image', $f['filename'] );
                    continue;
                }

                $now             = DateTimeWrapper::Now();
                $p               = new Photo();
                $p->exif         = exif_read_data( $f['path'], null, true );
                $p->albumId      = $album->albumId;
                $p->album        = $album;
                $p->createdAt    = $now;
                $p->filename     = sprintf( '%04d.jpg', $maxId );
                $p->fileSize     = filesize( $f['path'] );
                $p->originalName = $f['filename'];

                $photoDate = $p->exif && !empty( $p->exif['EXIF'] ) ? ArrayHelper::GetValue( $p->exif['EXIF'], 'DateTimeOriginal', null ) : null;
                if ( $photoDate ) {
                    $p->photoDate = $photoDate ? DateTime::createFromFormat( 'Y:m:d G:i:s', $photoDate ) : null;
                    $p->statusId  = StatusUtility::Enabled;
                }

                $hdPath = self::GetRealPath( $album, self::HD ) . $p->filename;
                if ( !ImageHelper::Resize( $f['path'], $hdPath, $sph->GetBigImageSize(), $sph->GetBigImageSize(), $sph->GetBigImageQuality(), true ) ) {
                    Logger::Warning( 'Failed to create hd image %s', $hdPath );
                    continue;
                }

                $p->fileSizeHd = filesize( $hdPath );

                $thumbPath = self::GetRealPath( $album, self::Thumbs ) . $p->filename;
                if ( !ImageHelper::Resize( $hdPath, $thumbPath, PhotoUtility::$ThumbSize[0], PhotoUtility::$ThumbSize[1], $sph->GetSmallImageQuality(), false ) ) {
                    Logger::Warning( 'Failed to create thumb image %s', $thumbPath );
                    break;
                }

                if ( !$p->exif ) {
                    $p->exif = [ ];
                }

                if ( !PhotoFactory::Add( $p ) ) {
                    Logger::Error( 'Failed to add photo to database', $p->filename );
                } else {
                    Logger::Debug( 'Successfully added photo %s', $p->filename );
                    $changed = true;
                }
            }

            if ( $changed ) {
                $album->modifiedAt = DateTimeWrapper::Now();
                self::FillMetaInfo( $album );

                if ( !AlbumFactory::Update( $album ) ) {
                    Logger::Error( 'Failed to update Album metainfo' );
                }
            }
        }


        /**
         * Get Real Path
         * @param Album  $album
         * @param string $folderType
         * @return string
         */
        public static function GetRealPath( Album $album, $folderType ) {
            return Site::GetRealPath( 'albums://' ) . sprintf( '%d/%s/%s/', $album->startDate->format( 'Y' ), $album->folderPath, $folderType );
        }


        /**
         * Fill Meta Info
         * @param Album $album
         */
        public static function FillMetaInfo( Album $album ) {
            $count = PhotoFactory::Count( [ 'albumId' => $album->albumId, 'pageSize' => 1, 'statusId' => StatusUtility::Enabled ], [ BaseFactory::WithoutPages => true ] );
            list( $fs, $fh ) = self::GetFileSize( $album );

            $photos = PhotoFactory::Get( [ 'albumId' => $album->albumId, 'pageSize' => self::DefaultPreviewCount ]
                , [ BaseFactory::WithColumns => '"photoId"', BaseFactory::OrderBy => '"orderNumber", "photoDate" ' . ( $album->isDescSort ? 'DESC' : 'ASC' ) ]
            );

            $album->metaInfo = [
                'count'    => $count,
                'size'     => $fs,
                'sizeHd'   => $fh,
                'photoIds' => array_keys( $photos ),
            ];
        }


        /**
         * Get Sum FileSizes for Album
         * @param Album $album
         * @return int[] fileSize, fileSizeHd
         */
        public static function GetFileSize( Album $album ) {
            $conn = ConnectionFactory::Get();
            $sql  = <<<sql
                SELECT sum( "fileSize" ) AS fs, sum( "fileSizeHd" ) AS fh
                FROM "photos"
                WHERE "statusId" = 1 AND "albumId" = @albumId
sql;

            $cmd = new SqlCommand( $sql, $conn );
            $cmd->SetInt( '@albumId', $album->albumId );
            $ds = $cmd->Execute();


            if ( $ds->Next() ) {
                return [ $ds->GetInteger( 'fs' ), $ds->GetInteger( 'fh' ) ];
            }

            return [ 0, 0 ];
        }


        /**
         * Get With Tag Ids
         * @param int[] $ids
         * @return string
         */
        public static function GetWithTagIdSql( $ids ) {
            if ( !$ids ) {
                return '';
            }

            return ' AND "tagIds" && ' . ConnectionFactory::Get()->GetComplexType( 'int[]' )->ToDatabase( $ids );
        }


        /**
         * Fill First Photo
         * @param Album[] $albums
         * @param Album[] $_
         * @return array
         */
        public static function FillFirstPhoto( $albums, $_ = null ) {
            $ids  = [ ];
            $list = func_get_args();
            /** @var Album[] $aa */
            foreach ( $list as $aa ) {
                foreach ( $aa as $a ) {
                    $ids[$a->albumId] = $a->PhotoId();
                }
            }

            if ( $ids ) {
                $photos = PhotoFactory::Get( [ '_photoId' => $ids ], [ BaseFactory::WithoutPages => true ] );
                foreach ( $list as $aa ) {
                    foreach ( $aa as $a ) {
                        $a->Photo = ArrayHelper::GetValue( $photos, $ids[$a->albumId] );
                    }
                }
            }

            return $ids;
        }


        /**
         * Fill Tags for Album
         * @param Tag[] $tags tag map
         * @param Album $album
         */
        public static function FillTags( $tags, $album ) {
            if ( !$album->tagIds ) {
                return;
            }

            foreach ( $album->tagIds as $tagId ) {
                $tag = ArrayHelper::GetValue( $tags, $tagId );
                if ( $tag ) {
                    $album->Tags[$tagId] = $tag;
                }

                if ( $tag->path ) {
                    foreach ( $tag->path as $tId ) {
                        $t = ArrayHelper::GetValue( $tags, $tId );
                        if ( $t && !in_array( $tId, $album->tagIds, true ) ) {
                            $album->AllTags[$tId] = $t;
                        }
                    }
                }
            }
        }


        /**
         * Fill Albums
         * @param AlbumByTag[] $albumsByTag
         * @return Album[]
         */
        public static function FillAlbums( $albumsByTag ) {
            $ids = [ ];
            foreach ( $albumsByTag as $at ) {
                $ids = array_merge( $ids, $at->AlbumIds );
            }
            //unset( $at );

            $ids = array_unique( $ids );
            if ( !$ids ) {
                return [];
            }

            $albums = AlbumFactory::Get( [ '_albumId' => $ids ], [ BaseFactory::WithoutPages => true ] );
            foreach ( $albumsByTag as $at ) {
                foreach ( $at->AlbumIds as $id ) {
                    $at->Albums[] = $albums[$id]; // possible null index?
                }
            }

            return $albums;
        }


        /**
         * Get Search Custom Sql for AlbumFactory
         * @param int[] $tagIds
         * @param bool $isStory
         * @return string
         */
        public static function GetSearchCustomSql( $tagIds, $isStory ) {
            $sql = '';

            if ( $tagIds ) {
                $sql .= self::GetWithTagIdSql( $tagIds );
            }

            if ( $isStory ) {
                $sql .= ' AND ( "description" IS NOT NULL AND "description" != \'\' ) ';
            }

            return $sql;
        }
    }