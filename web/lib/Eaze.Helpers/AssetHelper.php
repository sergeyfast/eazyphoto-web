<?php
    /**
     * Asset Helper
     * @package Eaze
     * @subpackage Helpers
     * @author sergeyfast
     * @since 1.3
     * @static
     */
    class AssetHelper {

        /** JS Type */
        const JS  = 'js';

        /** CSS Type */
        const CSS = 'css';

        /** All browsers */
        const AnyBrowser = 'any';

        /** IE6 */
        const IE6 = 2;

        /** IE7 */
        const IE7 = 3;

        /** IE8 */
        const IE8 = 4;

        /** Any IE version */
        const IE = 5;

        /** <= IE 7 Wrapper */
        const LteIE7 = 7;

        /** <= IE 8 Wrapper */
        const LteIE8 = 8;

        /** <= IE 9 Wrapper */
        const LteIE9 = 9;

        /** < IE 8 Wrapper */
        const LtIE8 = 10;

        /** Line constants in container */
        const Line = '%line%';

        /**
         * Url of minify.php
         * @var string
         */
        static $MinifyUrl = '/shared/minify.php';

        /**
         * Filename of SVN Revision
         * @var string
         */
        protected static $revisionFilename = 'shared://.revision';


        /**
         * Enable PostProcess mode
         * @var bool
         */
        public static $PostProcess = true;


        /**
         * Flush points for PostProcess
         * @var array
         */
        private static $flushPoints = array();

        /**
         * Browser Modes
         * @var array
         */
        public static $BrowserModes = array(
            self::AnyBrowser
            , self::IE
            , self::IE6
            , self::IE7
            , self::IE8
            , self::LteIE7
            , self::LteIE8
            , self::LteIE9
            , self::LtIE8
        );


        /**
         * Conditional Comments
         * @var array
         */
        public static $WrapperTemplates = array(
            self::AnyBrowser => '%s'
            , self::IE6      => '<!--[if IE 6]>%s<![endif]-->'
            , self::IE7      => '<!--[if IE 7]>%s<![endif]-->'
            , self::IE8      => '<!--[if IE 8]>%s<![endif]-->'
            , self::LteIE7   => '<!--[if lte IE 7]>%s<![endif]-->'
            , self::LteIE8   => '<!--[if lte IE 8]>%s<![endif]-->'
            , self::LteIE9   => '<!--[if lte IE 9]>%s<![endif]-->'
            , self::IE       => '<!--[if IE]>%s<![endif]-->'
            , self::LtIE8    => '<!--[if lt IE 8]>%s<![endif]-->'
        );

        /**
         * Type Templates
         * @var array
         */
        public static $TypeTemplates = array(
            self::JS => array(
                'src'        => '<script type="text/javascript" src="%s"></script>'
                , self::Line => '<script type="text/javascript">%s</script>'
            )
            , self::CSS => array(
                'src'        => '<link rel="stylesheet" type="text/css" href="%s" />'
                , self::Line => '<style type="text/css">%s</style>'
            )
        );

        /**
         * Container for Assets
         * @var array
         */
        protected static $container = array(
            self::JS    => array()
            , self::CSS => array()
        );


        /**
         * Get SVN Revision
         * @static
         * @return string
         */
        public static function GetRevision() {
            static $revision;

            if ( empty( $revision ) ) {
                $filename = Site::GetRealPath( self::$revisionFilename );
                if ( is_file( $filename ) ) {
                    $revision = trim( file_get_contents( $filename ) );
                } else {
                    $revision = 1;
                }
            }

            return $revision;
        }


        /**
         * Add file to Container
         * @static
         * @param  string $type  container type(self::JS or self::CSS)
         * @param  string $file  filename
         * @param string $mode   browser mode
         * @return void
         */
        protected static function addFile( $type, $file, $mode = self::AnyBrowser ) {
            self::$container[$type][$mode][$file] = $file;
        }


        /**
         * Add Line to Container
         * @static
         * @param  string $type  container type(self::JS or self::CSS)
         * @param string $line   line
         * @param string $mode   browser mode
         * @return void
         */
        protected static function addLine( $type, $line, $mode = self::AnyBrowser ) {
            if ( array_key_exists( $mode, self::$container[$type] )
                    && array_key_exists( self::Line, self::$container[$type][$mode] ) )
            {
                self::$container[$type][$mode][self::Line] .= $line;
            } else {
                self::$container[$type][$mode][self::Line]  = $line;
            }
        }


        /**
         * Delete File
         * @static
         * @param  string $type
         * @param  string $file
         * @param string $mode
         * @return bool
         */
        protected static function deleteFile( $type, $file, $mode = self::AnyBrowser ) {
            $result = false;
            if ( array_key_exists( $mode, self::$container[$type] )
                    && array_key_exists( $file, self::$container[$type][$mode] ) ) {
                unset( self::$container[$type][$mode][$file] );
                $result = true;
            }

            return $result;
        }


        /**
         * Set Flush Point
         * @static
         * @param  string $type  asset type
         * @param $minify
         * @param $hostname
         * @param $maxGroups
         * @return string
         */
        protected static function setFlushPoint( $type, $minify, $hostname, $maxGroups ) {
            if ( empty( self::$flushPoints[$type] ) ) {
                $id = '{%' . uniqid( $type ) . '%}';
                self::$flushPoints[$type] = compact( 'id', 'minify', 'hostname', 'maxGroups' );
            }

            return self::$flushPoints[$type]['id'];
        }


        /**
         * Flush Browser Mode
         * @static
         * @param string $type
         * @param string $mode
         * @param bool   $minify
         * @param string $hostname
         * @param int    $maxGroups
         * @return string
         */
        protected static function flushMode( $type, $mode = self::AnyBrowser, $minify, $hostname, $maxGroups  ) {
            $result = '';
            if ( empty( self::$container[$type][$mode] ) ) {
                return $result;
            }

            $lines = '';
            $files = array();
            foreach( self::$container[$type][$mode] as $file => $content ) {
                if ( $file == self::Line ) {
                    $lines .= sprintf( self::$TypeTemplates[$type][self::Line], $content );
                } else {
                    $files[] = $file;
                }
            }

            // set files
            if ( !empty( $files ) ) {
                $paths = array();
                foreach ( $files as $file ) {
                    $paths[] = $minify ?  Site::TranslateUrlWithPath( $file, $hostname ) : Site::GetWebPath( $file, $hostname );
                }

                // set result
                $revision = self::GetRevision();
                if ( $minify ) {
                    $url        = Site::GetWebPath( self::$MinifyUrl, $hostname );
                    $pathGroups = array_chunk( $paths, $maxGroups );

                    foreach( $pathGroups as $paths ) {
                        $fullUrl = $url .  '?' . $revision . '&amp;files=' . implode( ',', $paths );
                        $result .= sprintf( self::$TypeTemplates[$type]['src'], $fullUrl );
                    }
                } else {
                    foreach( $paths as $path ) {
                        $result .= sprintf( self::$TypeTemplates[$type]['src'], $path . '?' . $revision );
                    }
                }
            }

            $result .= $lines;

            // return wrapped result & clear mode
            self::$container[$type][$mode] = array();

            return sprintf( self::$WrapperTemplates[$mode], $result );
        }


        /**
         * Post Process
         * @static
         * @param  string $html
         * @return string replaced html
         */
        public static function PostProcess( $html ) {
            if ( !self::$PostProcess || empty( self::$flushPoints ) ) {
                return $html;
            }

            $search  = array();
            $replace = array();
            foreach( self::$flushPoints as $type => $point ) {
                $search[] = $point['id'];
                $output   = '';
                foreach ( self::$BrowserModes as $mode ) {
                    $output .= self::flushMode( $type, $mode, $point['minify'], $point['hostname'], $point['maxGroups'] );
                }
                $replace[] = $output;
            }

            return str_replace( $search, $replace, $html );
        }
    }
?>