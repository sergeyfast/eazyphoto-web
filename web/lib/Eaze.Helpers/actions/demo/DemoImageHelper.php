<?php
    class DemoImageHelper {
        
        private $small_x = 93;
        private $small_y = 93;
        private $big_x   = 200;
        private $big_y   = 200;
        
        private $quality = 90;
        private $small_scale   = true;
        private $big_scale     = true;
        
        // temp names
        private $smallImage = "ihd_small.jpg";
        private $bigImage   = "ihd_big.jpg";
        private $tempPath   = "temp://";
        
        /**
         * Set Settings
         *
         */
        private function setSettings() {
            $small_x     = Request::getInteger( "small_x" );
            $small_y     = Request::getInteger( "small_y" );
            $big_x       = Request::getInteger( "big_x" );
            $big_y       = Request::getInteger( "big_y" );
            
            $this->small_x = empty( $small_x ) ? $this->small_x : $small_x;
            $this->small_y = empty( $small_y ) ? $this->small_y : $small_y;
            $this->big_x   = empty( $big_x ) ? $this->big_x : $big_x;
            $this->big_y   = empty( $big_y ) ? $this->big_y : $big_y;
            
            $this->small_scale = Request::getBoolean( "small_scale" );
            $this->big_scale   = Request::getBoolean( "big_scale" );
            
            Response::setInteger( "small_x", $this->small_x );
            Response::setInteger( "small_y", $this->small_y );
            Response::setInteger( "big_x",   $this->big_x );
            Response::setInteger( "big_y",   $this->big_y );
            
            $tempPath = Request::getString( "tempPath" );
            if ( !empty( $tempPath ) ) {
                $this->tempPath  = $tempPath;
            }
            
            Response::setBoolean( "small_scale", $this->small_scale );
            Response::setBoolean( "big_scale",   $this->big_scale );
            Response::setString( "smallImage",   $this->tempPath . $this->smallImage  );
            Response::setString( "bigImage",     $this->tempPath . $this->bigImage  );
        }
        
        
        /**
         * Execute DemoImageHelper
         */
        public function Execute() {
            $file    =  Request::getFile( "file" );
            $sendForm = Request::getInteger( "sendForm" );
            
            $this->setSettings();
            
            if ( $sendForm == 1 ) {
                
                $smallRealPath = Site::GetRealPath( $this->tempPath . $this->smallImage );
                $bigImagePath  = Site::GetRealPath( $this->tempPath . $this->bigImage );
                if ( file_exists( $smallRealPath ) ) unlink( $smallRealPath );
                if ( file_exists( $bigImagePath ) ) unlink( $bigImagePath );
                
                $result = ImageHelper::Resize(
                    $file["tmp_name"]
                    , $smallRealPath
                    , $this->small_x
                    , $this->small_y
                    , $this->quality
                    , $this->small_scale
                );
                
                $result = ImageHelper::Resize(
                    $file["tmp_name"]
                    , $bigImagePath
                    , $this->big_x
                    , $this->big_y
                    , $this->quality
                    , $this->big_scale
                );
                
                if ( $result ) {
                    Response::setBoolean( "result", true );
                }
            }
        }
    }
?>