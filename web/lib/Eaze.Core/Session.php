<?php
    /**
     * Session
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class Session {

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
         * Get or Set Value
         *
         * @param string $mode     the mode
         * @param string $key      the key value
         * @param mixed  $value    the value
         * @param string $type     value type (string,int,etc..)
         */
        private static function value( $mode = MODE_GET, $key, $value = null, $type = null ) {
            if ( true == isset( self::$paramObject ) ) {

                if ( !self::$Initialized && $mode == MODE_SET  ) {
                    Logger::Error( 'Session already stopped' );
                    return null;
                }

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

                if ( !session_start() ) {
                    Logger::Error( 'Couldn\'t start session' );
                }

                self::$paramObject = new ParamObject( $_SESSION );
            }
        }


        /**
         * Get session id
         *
         * @return string
         */
        public static function getId() {
            return session_id();
        }


        /**
         * Set session id
         *
         * @param string $id
         * @return string
         */
        public static function setId( $id ) {
            return session_id( $id );
        }


        /**
         * Get session name
         *
         * @return string
         */
        public static function getName() {
            return session_name();
        }


        /**
         * Set session name
         *
         * @param string $name
         * @return string
         */
        public static function setName( $name ) {
            return session_name( $name );
        }


        /**
         * Commit session
         *
         * @return void
         */
        public static function Commit() {
            if ( true == isset( self::$paramObject ) ) {
                $_SESSION = self::$paramObject->getParameters();
            }

            session_commit();
            self::$Initialized = false;
        }


        /**
         * Destroy Session
         *
         * @return bool
         */
        public static function Destroy() {
            self::$paramObject = null;
            self::$Initialized  = false;

            return session_destroy();
        }


        /**
         * Get Session Save Path
         *
         * @return string
         */
        public static function getSavePath() {
            return session_save_path();
        }

        /**
         * Update the current session id with a newly generated one
         *
         * @param bool $deleteOldSession
         * @return bool
         */
        public static function regenerateId( $deleteOldSession = false ) {
            return session_regenerate_id( $deleteOldSession );
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