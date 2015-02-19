<?php
    use Eaze\Core\Convert;
    use Eaze\Core\DirectoryInfo;
    use Eaze\Core\FileInfo;
    use Eaze\Helpers\ImageHelper;
    use Eaze\Model\BaseFactory;
    use Eaze\Site\Site;

    /**
     * VFS Utility
     *
     * @package    Base
     * @subpackage VFS
     * @author     Sergeyfast
     */
    class VfsUtility {

        /**
         * Folder Input Type Control
         */
        const InputTypeFolder = 'folder';

        /**
         * File Input Type Control
         */
        const InputTypeFile = 'file';

        /**
         * Vfs Temp Path
         */
        const TempPath = 'temp://';

        /**
         * Vfs Root Dir
         */
        const RootDir = 'vfs://';

        /**
         * Vfs Root Folder Id, default is true
         */
        const RootFolderId = 1;

        /**
         * Auto Resize Images After Upload Via $ResizableSettings. Global Setting
         *
         * @var bool
         */
        public static $Resizable = false;

        /**
         * Use Salted FileNames. Global Setting
         *
         * @var bool
         */
        public static $SaltedFileNames = false;


        /**
         * Settings for AutoResize
         *
         * @var array
         */
        public static $ResizableSettings = array(
            'prefix' => 'original_',
            'keep'   => true,
            'modes'  => [
                [
                    'prefix'  => 'small_',
                    'width'   => 93,
                    'height'  => 93,
                    'scale'   => false,
                    'quality' => 90,
                ]
            ]
        );


        /**
         * Get Current Dir Real Path
         *
         * @return string
         */
        public static function GetCurrentDirRealPath() {
            $path = Site::GetRealPath( sprintf( 'vfs://%s/', date( 'Ym' ) ) );

            if ( !file_exists( $path ) && !is_dir( $path ) ) {
                mkdir( $path );
            }

            return $path;
        }


        /**
         * Change FolderId to Jailed if Needed
         * @param int $folderId
         * @param int $jailedFolderId
         * @return mixed
         */
        public static function SetJailRoot( $folderId, $jailedFolderId ) {
            if ( $jailedFolderId ) {
                $jailed = true;

                do {
                    $folderId = Convert::ToInt( $folderId );
                    if ( !$folderId ) {
                        break;
                    }

                    $folder = VfsFolderFactory::GetById( $folderId );
                    if ( !$folder ) {
                        break;
                    }

                    $path = VfsFolderFactory::GetBranch( $folder );
                    if ( !empty( $path[$jailedFolderId] ) ) {
                        $jailed = false;
                    }
                } while ( false );

                if ( $jailed ) {
                    $folderId = $jailedFolderId;
                }
            }

            return $folderId;
        }


        /**
         * Set Jail After Response
         * @param int         $jailedFolderId
         * @param VfsFolder[] $branch
         */
        public static function CutJailedRoot( $jailedFolderId, &$branch ) {
            if ( !empty( $jailedFolderId ) && !empty( $branch[$jailedFolderId] ) ) {
                $i = 0;
                $f = current( $branch );
                while ( $f->folderId != $jailedFolderId ) {
                    array_shift( $branch );
                    $f = current( $branch );
                    if ( $i++ > 30 ) {
                        break;
                    }
                }
            }
        }


        /**
         * Create Folder
         *
         * @param  int    $rootFolderId
         * @param  string $name
         * @throws Exception
         * @return bool
         */
        public static function CreateFolder( $rootFolderId, $name ) {
            if ( !$rootFolderId || !$name ) {
                throw new Exception( 'Invalid Params', 404 );
            }

            $folder = VfsFolderFactory::GetById( $rootFolderId );
            if ( !$folder ) {
                throw new Exception( 'Invalid folder', 404 );
            }

            $newFolder                 = new VfsFolder();
            $newFolder->parentFolderId = $rootFolderId;
            $newFolder->parentId       = $rootFolderId;
            $newFolder->parent         = $folder;
            $newFolder->title          = $name;
            $newFolder->statusId       = 1;

            return VfsFolderFactory::Add( $newFolder );
        }


        /**
         * Save Temp File
         *
         * @param array $requestFile PHP Standard Request
         * @return bool|array [name,path,size,type,normal,relpath]
         */
        public static function SaveTempFile( $requestFile ) {
            $result = false;
            if ( empty( $requestFile ) ) {
                return $result;
            }

            $extension = DirectoryInfo::GetExtension( $requestFile['name'] );
            $tempFile  = sprintf( '%s%s.%s'
                , self::TempPath
                , md5( $requestFile['tmp_name'] . time() )
                , $extension
            );

            if ( move_uploaded_file( $requestFile['tmp_name'], Site::GetRealPath( $tempFile ) ) ) {
                $result = array(
                    'name'    => $requestFile['name'],
                    'path'    => Site::GetRealPath( $tempFile ),
                    'size'    => $requestFile['size'],
                    'type'    => $requestFile['type'],
                    'normal'  => basename( $requestFile['name'], '.' . $extension ),
                    'relpath' => basename( $tempFile ),
                );
            }

            return $result;
        }


        /**
         * Create File
         * @param int    $folderId
         * @param string $name
         * @param string $path full path
         * @param null   $type
         * @return bool|int false or vfs file id
         */
        public static function CreateFile( $folderId, $name, $path, $type = null ) {
            $result = false;

            // Check For Resizable
            if ( self::$Resizable && false === ImageHelper::IsImage( $path ) ) {
                VfsUtility::$Resizable = false;
            }

            if ( VfsUtility::$Resizable ) {
                $settings = VfsUtility::$ResizableSettings;

                // check for keep
                if ( $settings['keep'] ) {
                    $originalPath = null;
                    $result       = self::AddFile( $folderId, $settings['prefix'] . $name, $path, $type, null, $originalPath );
                } else {
                    $originalPath = $path;
                }

                // resizing and adding to db
                foreach ( $settings['modes'] as $mode ) {
                    $resizedPath = Site::GetRealPath( 'temp://for_resize.jpg' );
                    if ( file_exists( $resizedPath ) ) {
                        unlink( $resizedPath );
                    }

                    // resize
                    $opResult = ImageHelper::Resize( $originalPath, $resizedPath, $mode['width'], $mode['height'], $mode['quality'], $mode['scale'] );
                    if ( $opResult ) { //add
                        $result = self::AddFile( $folderId, $mode['prefix'] . $name, $resizedPath, 'image/jpeg' );
                    }
                }


                // check for keep flag
                if ( empty( $settings['keep'] ) && file_exists( $originalPath ) ) {
                    unlink( $originalPath );
                }
            } else {
                $result = self::AddFile( $folderId, $name, $path, $type );
            }

            if ( $result ) {
                $result = VfsFileFactory::GetCurrentId();
            }

            return $result;
        }


        /**
         * Get Collapsed Files By Folder Id
         * Used with AutoResize
         *
         * @param string $folderId
         * @param array  $groups prefixes from autoresize, like image_ small_ big_
         * @return array
         */
        public static function GetCollapsedFilesByFolderId( $folderId, $groups ) {
            $result = array();
            if ( empty( $folderId ) ) {
                return $result;
            }

            $search["folderId"] = $folderId;
            $files              = VfsFileFactory::Get( $search, array( BaseFactory::WithoutPages => true ) );

            if ( empty( $files ) ) {
                return $result;
            }

            foreach ( $files as $file ) {
                foreach ( $groups as $group ) {
                    if ( strpos( $file->title, $group ) === 0 ) {
                        $name                  = substr( $file->title, strlen( $group ) );
                        $result[$name][$group] = $file;
                        break;
                    }
                }

            }

            return $result;
        }


        /**
         * Add File to Database
         *
         * @param integer $folderId    folder id
         * @param string  $name        file name
         * @param string  $path        full path to temp file
         * @param string  $type        mime type
         * @param string  $connectionName
         * @param string  $newFileName output value for new filename
         * @return bool|VfsFile
         */
        public static function AddFile( $folderId, $name, $path, $type = null, $connectionName = null, &$newFileName = null ) {
            $result = false;
            do {
                if ( empty( $folderId ) || empty( $name ) || empty( $path ) ) {
                    break;
                }

                /** @var VfsFolder $folder */
                $folder = VfsFolderFactory::GetById( $folderId, array(), array(), $connectionName );
                if ( empty( $folder ) ) {
                    break;
                }

                // Save to Database
                $result = self::AddFileToFolder( $folder, $name, $path, $type, $connectionName, $newFileName );
            } while ( false );

            return $result;
        }


        /**
         * Add File to Database
         *
         * @param VfsFolder $folder
         * @param string    $name        file name
         * @param string    $path        full path to temp file
         * @param string    $type        mime type
         * @param string    $connectionName
         * @param string    $newFileName output value for new filename
         * @return bool|VfsFile
         */
        public static function AddFileToFolder( $folder, $name, $path, $type = null, $connectionName = null, &$newFileName = null ) {
            if ( empty( $folder ) || empty( $name ) || empty( $path ) ) {
                return false;
            }

            $salt = ( VfsUtility::$SaltedFileNames ) ? "_" . substr( md5( time() . "-" . microtime() ), 0, 8 ) : "";

            /** Add File */
            $fileId      = VfsFileFactory::GetCurrentId( $connectionName ) + 1;
            $newFileName = sprintf( "%s%s_%s%s.%s"
                , self::GetCurrentDirRealPath()
                , $folder->folderId
                , $fileId
                , $salt
                , DirectoryInfo::GetExtension( $path )
            );

            $tmpFile = new FileInfo( $path );
            if ( $tmpFile->MoveTo( $newFileName ) ) {
                $file             = new VfsFile();
                $file->title      = $name;
                $file->statusId   = 1;
                $file->fileId     = $fileId;
                $file->fileExists = file_exists( $newFileName );
                $file->folderId   = $folder->folderId;
                $file->fileSize   = $tmpFile->GetFileSize();
                $file->mimeType   = ( empty( $type ) ) ? $tmpFile->GetType() : $type;
                $file->path       = sprintf( "%s/%s_%s%s.%s", date( "Ym" ), $folder->folderId, $fileId, $salt, $tmpFile->GetExtension() );

                if ( ImageHelper::IsImage( $newFileName ) ) {
                    $file->params = ImageHelper::GetImageSizes( $newFileName );
                }

                $result = VfsFileFactory::Add( $file, $connectionName );

                return ( $result ) ? $file : $result;
            }

            return false;
        }
    }

