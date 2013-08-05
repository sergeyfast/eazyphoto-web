<?php
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
            foreach ( array( self::HD, self::Source, self::Thumbs ) as $folderType ) {
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
                if ( $album->statusId != StatusUtility::InQueue ) {
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
                $errHd             = $client->AddSyncFolder( $pathHd, $hdSecret['secret'] );
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

            foreach( $photos as $photo ) {
                $t = ltrim( $photo->filename,  '0' );
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
            $photos  = PhotoFactory::Get( array( 'albumId' => $album->albumId ), array( BaseFactory::WithoutPages => true, BaseFactory::WithColumns => '`photoId`,`filename`, `originalName`, `statusId`' ) );
            $fileDir = DirectoryInfo::GetInstance( self::GetRealPath( $album, self::Source ) );
            $cPhotos = ArrayHelper::Collapse( $photos, 'originalName', false );
            $maxId   = self::GetMaxPhoto( $cPhotos );
            $changed = false;


            foreach( $fileDir->GetFiles() as $f ) {
                $maxId ++;

                $name = $f['filename'];
                if ( mb_strpos( mb_strtolower( $name ), 'sync' ) !== false ) {
                    continue;
                }

                if ( !empty( $cPhotos[$name] ) ) {
                    Logger::Debug( '%s already in db', $f['filename'] );
                    continue;
                }

                if ( !exif_imagetype( $f['path'] )  ) {
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

                $photoDate       = $p->exif && !empty( $p->exif['EXIF'] ) ? ArrayHelper::GetValue( $p->exif['EXIF'], 'DateTimeOriginal', null ): null;
                if ( $photoDate ) {
                    $p->photoDate    = $photoDate ? DateTime::createFromFormat( 'Y:m:d G:i:s', $photoDate ) : null;
                    $p->statusId     = StatusUtility::Enabled;
                }

                $hdPath = self::GetRealPath( $album, self::HD ) .  $p->filename;
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
                    $p->exif = array();
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

                if (!AlbumFactory::Update( $album ) ) {
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
            $count = PhotoFactory::Count( array( 'albumId' => $album->albumId, 'pageSize' => 1, 'statusId' => 1 ), array( BaseFactory::WithoutPages => true ) );
            list( $fs, $fh ) = self::GetFileSize( $album );

            $photos = PhotoFactory::Get( array( 'albumId' => $album->albumId, 'pageSize' => self::DefaultPreviewCount )
                , array( BaseFactory::WithColumns => '`photoId`', BaseFactory::OrderBy => 'ISNULL(`orderNumber`), `orderNumber`, `photoDate` ' . ( $album->isDescSort ? 'DESC' : 'ASC' )  )
            );

            $album->metaInfo = array(
                'count'      => $count
                , 'size'     => $fs
                , 'sizeHd'   => $fh
                , 'photoIds' => array_keys( $photos )
            );
        }


        /**
         * Get Sum FileSizes for Album
         * @param Album $album
         * @return int[] fileSize, fileSizeHd
         */
        public static function GetFileSize( Album $album ) {
            $conn = ConnectionFactory::Get();
            $sql  = <<<sql
                SELECT sum( `fileSize` ) as fs, sum( `fileSizeHd` ) as fh
                FROM `photos`
                WHERE `statusId` = 1 AND `albumId` = @albumId
sql;

            $cmd = new SqlCommand( $sql, $conn );
            $cmd->SetInt( '@albumId', $album->albumId );
            $ds = $cmd->Execute();


            if ( $ds->Next() ) {
                return array( $ds->GetInteger( 'fs' ), $ds->GetInteger( 'fh' ) );
            }

            return array( 0, 0 );
        }
    }

?>