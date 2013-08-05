<?php
    /**
     * Upload File Action
     * @package    Base
     * @subpackage VFS
     * @author     Sergeyfast
     */
    class UploadFileAction {

        /**
         * Entry Point
         */
        public function Execute() {
            $response = array();

            VfsUtility::$Resizable         = Request::getBoolean( 'ufa_Resizable' );
            VfsUtility::$ResizableSettings = Request::getArray( 'ufa_Settings' );

            // Handle Flash upload Or browser Upload
            switch ( Page::$RequestData[1] ) {
                case 'temp':
                    $file = Request::getFile( 'fileUpload' );

                    if ( $file ) {
                        $response['file'] = VfsUtility::SaveTempFile( $file );
                    } else {
                        $response['error'] = 'vfsConstants.langEmptyFile';
                    }
                    break;
                case 'queue':
                    $file     = Request::getFile( 'Filedata' );
                    $folderId = Request::getInteger( 'folderId' );

                    if ( $file && $folderId ) {
                        $tempFile = VfsUtility::SaveTempFile( $file );
                        if ( !empty( $tempFile ) ) {
                            $response = VfsUtility::CreateFile( $folderId, $tempFile['normal'], $tempFile['path'], $tempFile['type'] );
                        }
                    } else {
                        $response['error'] = 'vfsConstants.langEmptyFile';
                    }

                    break;
            }

            // Flush Response
            header( 'Content-Type: application/json; charset=utf-8' );
            echo ObjectHelper::ToJSON( $response );
        }
    }

?>