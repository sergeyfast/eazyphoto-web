<?php
    class GetResizedImageAction {

        /**
         * Execute GetResizedImageAction
         */
        public function Execute() {
            $withDirCheck = Request::getBoolean( "withDirCheck" );
            $directoryId  = Request::getInteger( "f" );
            $fileId       = Page::$RequestData[1];

            if (( !empty( $withDirCheck ) && empty( $directoryId ))
                || (empty( $fileId )) ) {
                return "failure";
            }

            $file = VfsFileFactory::GetById( $fileId );
            if ( empty( $file ) ){
                return "failure";
            }

            if ( $withDirCheck ) {
                $folder = VfsFolderFactory::GetById( $directoryId );
                if ( empty( $folder ) || $folder->folderId != $file->folderId ) {
                    return "failure";
                }
            }

            /**
             * Resize Params
             */
            $width   = Request::getInteger( "width" );
            $height  = Request::getInteger( "height" );
            $quality = Request::getInteger( "quality" );
            $scale   = Request::getBoolean( "scale" );
            $file    = Site::GetRealPath( "vfs://" . $file->path );
            $image   = ImageHelper::Resize(  $file ,null, $width, $height, $quality, $scale );

            if ( $image === false ) {
                return "warning";
            }

            $expires = 60 * 60 * 24 * 3;
            $exp_gmt = gmdate("D, d M Y H:i:s", time() + $expires )." GMT";

            header("Content-type: image/jpeg");
            header("Expires: {$exp_gmt}");
            header("Cache-Control: public, max-age={$expires}");
            header("Pragma: !invalid");
            header("Content-Length: " . strlen( $image ));
            echo $image;
            die();
        }
    }
?>