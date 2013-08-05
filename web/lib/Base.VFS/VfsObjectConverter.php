<?php
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
                'id'         => $folder->folderId
                , 'name'     => $folder->title
                , 'parentId' => $folder->parentFolderId
            );

            if ( !empty( $folder->nodes ) ) {
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
            $filePaths = explode('.', $file->path );
            if ( !$file->params ) {
                $file->params = array();
            }

            return array(
                'id'          => $file->fileId
                , 'name'      => $file->title
                , 'path'      => Site::GetWebPath( VfsUtility::RootDir ) . $file->path
                , 'relpath'   => basename( $file->path, '.' .  array_pop( $filePaths ) )
                , 'size'      => $file->fileSize
                , 'date'      => $file->createdAt->DefaultFormat()
                , 'type'      => mb_strimwidth( $file->mimeType, 0, 32, '...' )
                , 'extension' => DirectoryInfo::GetExtension( $file->path )
                , 'params'    => $file->params
                , 'shortpath' => $file->path
                , 'width'     => ArrayHelper::GetValue( $file->params, 'width', null )
                , 'height'    => ArrayHelper::GetValue( $file->params, 'height', null )
            );
        }

    }

?>