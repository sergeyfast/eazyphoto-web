<?php
    define( 'METHOD_POST',    'post' );
    define( 'METHOD_GET',     'get' );
    define( 'METHOD_REQUEST', 'request' );

    /**
     * Web Request Class
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class Request {
        /**
         * Initialized flag
         *
         * @var boolean
         */
        public static $Initialized = false;

        /**
         * '$_REQUEST' Instance
         *
         * @var ParamObject
         */
        private static $requestInstance;

        /**
         * '$_GET' Instance
         *
         * @var ParamObject
         */
        private static $getInstance;

        /**
         * '$_POST' Instance
         *
         * @var ParamObject
         */
        private static $postInstance;


        /**
         * 'Host' Instance
         *
         * @var Host
         */
        private static $hostInstance;

        /**
         * Get Value
         *
         * @param string $key      the key value
         * @param string $method   get, post, or both
         * @param string $type     value type
         */
        private static function value( $mode = MODE_GET, $key, $value = null, $method = null, $type = null ) {
            switch ( strtolower( $method ) ) {
                case METHOD_REQUEST:
                    if ( isset( self::$requestInstance ) ) {
                        return self::$requestInstance->Value( $mode, $key, $value, $type );
                    }

                    break;
                case METHOD_GET:
                    if ( true == isset( self::$getInstance ) ) {
                        if ( $mode == MODE_SET ) {
                            Logger::Warning( 'Set for _GET is deprecated' );
                        }

                        return self::$getInstance->Value( $mode, $key, $value, $type );
                    }

                    break;
                case METHOD_POST:
                    if ( true == isset( self::$postInstance ) ) {
                        if ( $mode == MODE_SET ) {
                            Logger::Warning( 'Set for _POST is deprecated' );
                        }

                        return self::$postInstance->Value( $mode, $key, $value, $type );
                    }

                    break;
            }

            return null;
        }


        public static function getInteger( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_INTEGER );
        }

        public static function getBoolean( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_BOOLEAN );
        }

        public static function getString( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_STRING );
        }

        public static function getFloat( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_FLOAT );
        }

        public static function getArray( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_ARRAY );
        }

        public static function getObject( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_OBJECT );
        }

        public static function getParameter( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_PARAMETER );
        }

        public static function getValue( $key, $type, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, $type );
        }

        public static function getDateTime( $key, $method = METHOD_REQUEST ) {
            return self::value( MODE_GET, $key, null, $method, TYPE_DATETIME );
        }

        public static function setInteger( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_INTEGER );
        }

        public static function setBoolean( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_BOOLEAN );
        }

        public static function setString( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_STRING );
        }

        public static function setFloat( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_FLOAT );
        }

        public static function setArray( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_ARRAY );
        }

        public static function setObject( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_OBJECT );
        }

        public static function setParameter( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_PARAMETER );
        }

        public static function setValue( $key, $value, $type, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, $type );
        }

        public static function setDateTime( $key, $value, $method = METHOD_REQUEST ) {
            return self::value( MODE_SET, $key, $value, $method, TYPE_DATETIME );
        }

        /**
         * Get Parameters
         * @static
         * @param string $method
         * @return array
         */
        public static function getParameters( $method = METHOD_REQUEST ) {
             switch ( strtolower( $method ) ) {
                case METHOD_REQUEST:
                    if ( isset( self::$requestInstance ) ) {
                        return self::$requestInstance->GetParameters();
                    }

                    break;
                case METHOD_GET:
                    if ( isset( self::$getInstance ) ) {
                        return self::$getInstance->GetParameters();
                    }

                    break;
                case METHOD_POST:
                    if ( true == isset( self::$postInstance ) ) {
                        return self::$postInstance->GetParameters();
                    }

                    break;
            }

            return array();
        }

        /**
         * Init Request
         */
        public static function Init() {
            if ( ! self::$Initialized ) {
                self::$Initialized = true;

                Session::Init();
                Cookie::Init();
                Response::Init();

                self::$requestInstance = new ParamObject( $_REQUEST );
                self::$getInstance     = new ParamObject( $_GET );
                self::$postInstance    = new ParamObject( $_POST );
            }
        }


    	/**
    	 * Commit all changes in request
    	 */
    	public static function Commit() {
    	    if ( true == self::$Initialized ) {
                Session::Commit();
            }
    	}


    	/**
    	 * Get Current Host
    	 *
    	 * @return Host
    	 */
    	public static function GetHost() {
            if ( empty( self::$hostInstance ) ) {
                self::$hostInstance = Host::GetCurrentHost();
            }

    	    return self::$hostInstance;
    	}


        /**
         * Get $_SERVER Variable
         * @static
         * @param  string $key
         * @return string|null
         */
        public static function GetServerVariable( $key ) {
            if ( isset( $_SERVER[$key] ) ) {
                return $_SERVER[$key];
            }

            return null;
        }



        /**
         * Get Remote IP
         *
         * @return string X_REAL_IP, then REMOTE_ADDR
         */
        public static function GetRemoteIp() {
            if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
                 return $_SERVER['HTTP_X_REAL_IP'];
            } else if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
                return $_SERVER['REMOTE_ADDR'];
            } else {
                return null;
            }
        }


        /**
         * Get HTTP Host
         *
         * @return string $_SERVER['HTTP_HOST']
         */
        public static function GetHTTPHost() {
            return self::GetServerVariable( 'HTTP_HOST' );
        }


        /**
         * Get Referer
         *
         * @return string $_SERVER['HTTP_REFERER']
         */
        public static function GetReferer() {
            return self::GetServerVariable( 'HTTP_REFERER' );
        }


        /**
         * Get Script Name
         *
         * @return string $_SERVER['SCRIPT_NAME']
         */
        public static function GetScriptName() {
            return self::GetServerVariable( 'SCRIPT_NAME' );
        }


        /**
         * Get User Agent
         *
         * @return string $_SERVER['HTTP_USER_AGENT']
         */
        public static function GetUserAgent() {
            return self::GetServerVariable( 'HTTP_USER_AGENT' );
        }


        /**
         * Get Request Uri
         *
         * @return string Request Uri
         */
        public static function getRequestUri() {
            return self::GetServerVariable( 'REQUEST_URI' );
        }


        /**
         * Get File
         *
         * @param string $key
         * @return array
         */
        public static function GetFile( $key  )  {
            if ( !empty( $key ) ) {
                if ( isset( $_FILES[$key] ) ) {
                    return $_FILES[$key];
                } else {
                    return null;
                }
            }

            return null;
        }


        /**
         * Get Files
         *
         * @param string $key
         * @return array
         */
        public static function GetFiles( $key = null ) {
            $files = array();

            $sourceFiles = $_FILES;
            if ( !empty( $key ) ) {
                if ( isset( $_FILES[$key] ) ) {
                    $sourceFiles = $_FILES[$key];
                } else {
                    $sourceFiles = array();
                }
            }

            foreach ( $sourceFiles as $field => $params ) {
                foreach ( $params  as $key => $value) {
                    $files[$key][$field] = $value;
                }
            }

            return $files;
        }
    }
?>