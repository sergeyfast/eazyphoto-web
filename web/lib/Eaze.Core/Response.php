<?php
    /**
     * Response
     *
     * @package Eaze
     * @subpackage Eaze.Core
     */
    class Response  {
        /**
         * Initialized flag
         *
         * @var boolean
         */
        public static $Initialized = false;

        /**
         * Session Parameters
         *
         * @var ParamObject
         */
        private static $paramObject;

        /**
         * Enter description here...
         *
         * @param string $name  the file name
         * @param string $file      the file path
         */
        public static function SendFile( $file, $name =  null ) {
            $info = new FileInfo( $file );
            if ( empty( $name ) ) {
                $name = $info->GetName();
            }

            header('Content-Type: '.  $info->GetExtension() ) ;
            header('Content-Length: '. $info->GetFileSize() );
            header('Content-Disposition: attachment; filename="'. str_replace(' ', '%20', $name ) .'"');

            readfile( $file );
        }

        /**
         * Send Http Status Code
         *
         * @param string $code
         * @param string $message
         */
        public static function HttpStatusCode( $code, $message = '') {
            Package::$DisablePackageCompile = true;
            if (substr(php_sapi_name(), 0, 3) == 'cgi')  {
                header('Status: ' . $code . $message, true );
            } else {
                header( $_SERVER['SERVER_PROTOCOL'] . ' ' . $code . $message);
            }

            $fileName    = sprintf( '%s/%s.html', CONFPATH_ERRORS, $code);
            $xmlfileName = sprintf( '%s/%s.xml',  CONFPATH_ERRORS, $code);
            if ( file_exists( $xmlfileName ) ){
                $doc = new DOMDocument();
                $doc->load( $xmlfileName );
                /** @noinspection PhpUndefinedFieldInspection */
                $page = $doc->documentElement;

                new Page( $page, array( Site::GetCurrentURI() ), new DOMNodeList() );
            } elseif ( file_exists( $fileName ) ){
                /** @noinspection PhpIncludeInspection */
                include( $fileName );
            } else {
?>
<!DOCTYPE HTML PUBLIC '-//IETF//DTD HTML 2.0//EN'>
<html><head><title><?= $code ?> <?= $message ?></title></head><body><h1><?= $message ?></h1></body></html>
<?php
            }

           exit();
        }


        /**
         * Redirect to Url
         *
         * @param string $path
         */
        public static function Redirect( $path ) {
            if ( !headers_sent() ) {
                header( 'Location: ' . $path );
            } else {
                Logger::Error( 'Headers have been already sent. Cannot redirect to <a href="%s">%s</a>. Exiting.', $path, $path );
            }

            exit();
        }


        /**
         * Get or Set Value
         *
         * @param string $mode     the mode
         * @param string $key      the key value
         * @param mixed  $value    the value
         * @param string $type     value type (string,int,etc..)
         */
        private static function value( $mode = MODE_GET, $key, $value = null, $type = null ) {
            if ( true == isset( self::$paramObject ) ) {
                return self::$paramObject->value( $mode, $key, $value, $type );
            }

            return null;
        }


        /**
         * Init Session
         */
        public static function Init() {
            if ( ! self::$Initialized ) {
                self::$Initialized = true;
                Request::Init();

                self::$paramObject = new ParamObject();
            }
        }


        public static function getInteger( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_INTEGER );
        }

        public static function getBoolean( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_BOOLEAN );
        }

        public static function getString( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_STRING );
        }

        public static function getFloat( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_FLOAT );
        }

        public static function getArray( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_ARRAY );
        }

        public static function getObject( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_OBJECT );
        }

        public static function getParameter( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_PARAMETER );
        }

        public static function getDateTime( $key ) {
            return self::value( MODE_GET, $key, null, TYPE_DATETIME );
        }

        public static function getParameters() {
            if ( true == isset( self::$paramObject ) ) {
                return self::$paramObject->getParameters();
            }

            return null;
        }

        public static function setInteger( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_INTEGER );
        }

        public static function setBoolean( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_BOOLEAN );
        }

        public static function setString( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_STRING );
        }

        public static function setFloat( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_FLOAT );
        }

        public static function setArray( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_ARRAY );
        }

        public static function setObject( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_OBJECT );
        }

        public static function setParameter( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_PARAMETER );
        }

        public static function settDateTime( $key, $value ) {
            return self::value( MODE_SET, $key, $value, TYPE_DATETIME );
        }
    }
?>