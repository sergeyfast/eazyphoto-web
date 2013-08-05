<?php
    /**
     * Cookie
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class Cookie {

        /**
         * Initialized flag
         *
         * @var boolean
         */
        public static $Initialized = false;

        /**
         * Cookie Parameters
         *
         * @var ParamObject
         */
        private static $paramObject;


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
         * Init Cookies
         */
        public static function Init() {
            if ( ! self::$Initialized ) {
                self::$Initialized = true;
                Request::Init();

                self::$paramObject = new ParamObject( $_COOKIE );
            }
        }


        /**
         * Send a cookie
         *
         * @param string  $name     The name of the cookie
         * @param string  $value    The value of the cookie. This value is stored on the clients computer; do not store sensitive information.
         * @param int     $expires  The time the cookie expires. time()+60*60*24*30 will set the cookie to expire in 30 days. If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
         * @param string  $path     The path on the server in which the cookie will be available on. If set to '/', the cookie will be available within the entire domain.
         * @param string  $domain   The domain that the cookie is available. To make the cookie available on all subdomains of example.com then you'd set it to '.example.com'. The . is not required but makes it compatible with more browsers. Setting it to www.example.com will make the cookie only available in the www subdomain.
         * @param bool    $secure   Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client. When set to TRUE, the cookie will only be set if a secure connection exists. The default is FALSE. On the server-side, it's on the programmer to send this kind of cookie only on secure connection (e.g. with respect to $_SERVER["HTTPS"]).
         * @param bool    $httponly When TRUE the cookie will be made accessible only through the HTTP protocol. This means that the cookie won't be accessible by scripting languages, such as JavaScript. This setting can effectly help to reduce identity theft through XSS attacks (although it is not supported by all browsers). Added in PHP 5.2.0.
         * @return bool
         */
        public static function setCookie( $name, $value = null, $expires = null, $path = null, $domain = null, $secure = false, $httponly = true ) {
            $file = null;
            $line = null;

            $value = Convert::ToString( $value );

            if ( headers_sent( $file, $line ) ) {
                Logger::Error( 'Headers have been sent already by %s:%d' ,$file, $line );
                return false;
            }

            return setcookie( $name, $value, $expires, $path, $domain, $secure, $httponly );
        }


        /** Getters -------------------------------------------------------------- */
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
    }
?>