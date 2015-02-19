<?php

    use Eaze\Core\Request;
    use Eaze\Helpers\ImageHelper;
    use Eaze\Helpers\ObjectHelper;

    /**
     * Image Upload Control
     * @package    EazyPhoto
     * @subpackage Common
     * @author     sergeyfast
     */
    class ImageUploadControl {

        /**
         * Entry Point
         */
        public function Execute() {
            $result          = [ 'error' => null ];
            $folderId        = Request::getInteger( 'iuc_FolderId' );
            $settings        = Request::getArray( 'iuc_Settings' );
            $requestFolderId = Request::getInteger( 'folderId' );

            if ( $requestFolderId ) {
                $folderId = $requestFolderId;
            }

            do {
                if ( empty( $_FILES['Filedata'] ) ) {
                    $result['error'] = 'emptyFile';
                    break;
                }

                // Create & Resize
                $currentFileId                 = VfsFileFactory::GetCurrentId();
                $requestFile                   = $_FILES['Filedata'];
                VfsUtility::$Resizable         = true;
                VfsUtility::$ResizableSettings = $settings;

                if ( !ImageHelper::IsImage( $requestFile['tmp_name'] ) ) {
                    $result['error'] = 'notImage';
                    break;
                }

                $vfsResult =VfsUtility::CreateFile( $folderId, $requestFile['name'], $requestFile['tmp_name'], $requestFile['type'] );
                if ( !$vfsResult ) {
                    $result['error'] = 'vfsUtilityError';
                }

                if ( $currentFileId == $vfsResult ) {
                    $result['error'] = 'vfsUtilityErrorId';
                }

                // form $result array
                $i = 1;
                foreach ( $settings['modes'] as $modeName => $modeSettings ) {
                    $file              = VfsFileFactory::GetById( $currentFileId + $i );
                    $result[$modeName] = [
                        'id'   => $file->fileId,
                        'name' => $file->title,
                        'src'  => $file->path,
                    ];

                    $i++;
                }

            } while ( false );
            VfsUtility::$Resizable = false;

            // Form Result

            echo ObjectHelper::ToJSON( $result );
        }
    }