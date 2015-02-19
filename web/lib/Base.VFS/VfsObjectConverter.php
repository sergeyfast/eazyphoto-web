<?php
    use Eaze\Core\DirectoryInfo;
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Site\Site;

    /**
     * VfsObjectConverter
     * @package    Base
     * @subpackage VFS
     * @author     Sergeyfast
     */
    class VfsObjectConverter {

        /**
         * Get Vfs Folder Array
         * @param VfsFolder $folder
         * @return array (id, name, parentId)
         */
        public static function GetVfsFolder( VfsFolder $folder ) {
            $result = array(
                'id'       => $folder->folderId,
                'name'     => $folder->title,
                'parentId' => $folder->parentFolderId,
            );

            if ( $folder->nodes ) {
                $result['folders'] = array_map( 'VfsObjectConverter::GetVfsFolder', $folder->nodes );
            }

            return $result;
        }


        /**
         * Get Vfs File Array
         * @param VfsFile $file
         * @return array (id, name, path, size, type, extension, shortpath)
         */
        public static function GetVfsFile( VfsFile $file ) {
            $filePaths = explode( '.', $file->path );
            if ( !$file->params ) {
                $file->params = array();
            }

            return array(
                'id'        => $file->fileId,
                'name'      => $file->title,
                'path'      => Site::GetWebPath( VfsUtility::RootDir ) . $file->path,
                'relpath'   => basename( $file->path, '.' . array_pop( $filePaths ) ),
                'size'      => $file->fileSize,
                'sizeH'     => self::HumanSize( $file->fileSize, 2 ),
                'date'      => $file->createdAt->DefaultFormat(),
                'type'      => mb_strimwidth( $file->mimeType, 0, 32, '...' ),
                'extension' => DirectoryInfo::GetExtension( $file->path ),
                'params'    => $file->params,
                'shortpath' => $file->path,
                'width'     => ArrayHelper::GetValue( $file->params, 'width', null ),
                'height'    => ArrayHelper::GetValue( $file->params, 'height', null ),
            );
        }


        /**
         * Get human readable size
         * @param $bytes
         * @param $precision
         * @return array (value float, dimensions string)
         */
        public static function HumanSize( $bytes, $precision ) {
            $kilobyte = 1024;
            $megabyte = $kilobyte * 1024;
            $gigabyte = $megabyte * 1024;
            $terabyte = $gigabyte * 1024;

            if ( ( $bytes >= 0 ) && ( $bytes < $kilobyte ) ) {
                return array( $bytes, 'B' );
            } else {
                if ( ( $bytes >= $kilobyte ) && ( $bytes < $megabyte ) ) {
                    return array( round( $bytes / $kilobyte, $precision ), 'KB' );
                } else {
                    if ( ( $bytes >= $megabyte ) && ( $bytes < $gigabyte ) ) {
                        return array( round( $bytes / $megabyte, $precision ), 'MB' );
                    } else {
                        if ( ( $bytes >= $gigabyte ) && ( $bytes < $terabyte ) ) {
                            return array( round( $bytes / $gigabyte, $precision ), 'GB' );
                        } else {
                            return array( $bytes, 'B' );
                        }
                    }
                }
            }
        }
    }