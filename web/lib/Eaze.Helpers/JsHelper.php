<?php
    /**
     * Helps to render JS
     *
     * @package Eaze
     * @subpackage Helpers
     * @since 1.3
     * @author sergeyfast
     * @static
     */
    class JsHelper extends AssetHelper {

        /**
         * Minify scripts or not
         * @var bool
         */
        public static $Minify = true;

        /**
         * Max Group Size for minify
         * @var int
         */
        public static $MaxGroups = 20;

        /**
         * Default Hostname
         * @var string default hostname
         */
        public static $Hostname = 'static';

        /**
         * Current Type
         * @var string
         */
        private static $type = self::JS;

        /**
         * Add File
         * @param string $file single JS file
         * @param string $mode browser mode
         */
        public static function PushFile( $file, $mode = self::AnyBrowser ) {
            parent::addFile( self::$type, $file, $mode );
        }


        /**
         * Add multiple JS file
         * @param string[] $files array of JS files
         * @param string $mode browser mode
         */
        public static function PushFiles( $files, $mode = self::AnyBrowser ) {
            foreach ( $files as $file ) {
                self::PushFile( $file, $mode );
            }
        }


        /**
         * Add multiple JS grouped files
         * @param $groups array of js grouped files
         * @return void
         */
        public static function PushGroups( $groups ) {
            foreach( $groups as $mode => $files ) {
                self::PushFiles( $files, $mode );
            }
        }


        /**
         * Add JS line to JS code
         * @param string $line
         * @param string $mode browser mode
         */
        public static function PushLine( $line, $mode = self::AnyBrowser ) {
            parent::addLine( self::$type, $line, $mode );
        }


        /**
         * Flush All Modes
         * @return string
         */
        public static function Flush() {
            if ( self::$PostProcess ) {
                return self::setFlushPoint( self::$type, self::$Minify, self::$Hostname, self::$MaxGroups );
            }

            $result = '';
            foreach ( self::$BrowserModes as $mode ) {
                $result .= parent::flushMode( self::$type, $mode, self::$Minify, self::$Hostname, self::$MaxGroups );
            }

            return $result;
        }


        /**
         * Remove File from Queue
         * @static
         * @param string $file
         * @param string $mode
         * @return bool
         */
        public static function RemoveFile( $file, $mode = self::AnyBrowser ) {
            return parent::deleteFile( self::$type, $file, $mode );
        }


        /**
         * Init Helper
         * @static
         * @param bool   $minify
         * @param int    $maxGroups
         * @param string $hostname
         * @return void
         */
        public static function Init( $minify = true, $maxGroups = 25, $hostname = 'static' ) {
            self::$Minify    = $minify;
            self::$MaxGroups = $maxGroups;
            self::$Hostname  = $hostname;
        }
    }
?>