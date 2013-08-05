<?php
    /**
     * Convert Class
     *
     * @package Eaze
     * @subpackage Core
     * @see http://ru2.php.net/manual/ru/types.comparisons.php
     * @see http://ru2.php.net/manual/ru/language.types.type-juggling.php
     * @author sergeyfast
     */
    class Convert implements IConvert {

        /**
         * Default TimeZone
         *
         * @var DateTimeZone
         */
        protected static $defaultTimeZone = null;


        /**
         * Converts value to string
         * @static
         * @param  mixed $value
         * @return string|null
         */
        public static function ToString( $value ) {
            if ( $value === null
                 || is_object( $value )
            ) {
                return null;
            }

            return (string) $value;
        }


        /**
         * Converts value to integer  if it isn't object, array, empty string or null
         * @static
         * @param  mixed $value
         * @return int|null
         */
        public static function ToInt( $value ) {
            if ( $value === null
                 || $value === ''
                 || $value === 'null'
                 || $value === 'NULL'
                 || is_array( $value )
                 || is_object( $value )
            ) {
                return null;
            }

            return (int) $value;
        }


        /**
         * @see ToInt()
         * @static
         * @param  mixed $value
         * @return int|null
         */
        public static function ToInteger( $value ) {
            return self::ToInt( $value );
        }


        /**
         * Convert::ToFloat
         * @static
         * @param  mixed $value
         * @return float|null
         */
        public static function ToDouble( $value ) {
            return self::ToFloat( $value );
        }


        /**
         * Converts value to float if it isn't object, array, empty string or null
         * @static
         * @param  mixed $value
         * @return float|null
         */
        public static function ToFloat( $value ) {
            if ( $value === null
                 || $value === ''
                 || $value === 'null'
                 || $value === 'NULL'
                 || is_array( $value )
                 || is_object( $value )
            ) {
                return null;
            }

            return (float) $value;
        }


        /**
         * Converts value to bool (strings 'false' or 'f' are interpreted as false);
         * @static
         * @param  mixed $value
         * @return bool|null
         */
        public static function ToBoolean( $value ) {
            if ( $value === null
                 || $value === ''
                 || $value === 'null'
                 || $value === 'NULL' )
            {
                return null;
            }

            if ( is_string( $value ) ) {
                $result = strtolower( $value );
                if ( $result === 'false' || $result === 'f' ) {
                    return false;
                }
            }

            return (bool) $value;
        }


        /**
         * Converts Value to Array
         * @static
         * @param  mixed $value
         * @return array
         */
        public static function ToArray( $value ) {
            $result = (array) $value;

            return $result;
        }


        /**
         * Converts value to stdClass
         * @static
         * @param  mixed $value
         * @return stdClass
         */
        public static function ToObject( $value ) {
            $result = (object) $value;

            return $result;
        }


        /**
         * Converts Value to DateTime
         * @static
         * @param  $value
         * @param DateTimeZone|null $zone optional
         * @return DateTimeWrapper|null
         */
        public static function ToDateTime( $value, DateTimeZone $zone = null ) {
            if ( empty( $zone ) ) {
                if ( empty ( self::$defaultTimeZone ) ) {
                    self::$defaultTimeZone = new DateTimeZone( DEFAULT_TIMEZONE );
                }

                $zone = self::$defaultTimeZone;
            }

            $className = is_object( $value ) ? strtolower( get_class( $value ) ) : null;
            if ( 'datetime' == $className || 'datetimewrapper' == $className ) {
                return $value;
            } elseif ( $value !== null ) {
                $string = $value;

                try {
                    return new DateTimeWrapper( $string, $zone );
                } catch ( Exception $ex ) {
                    return null;
                }
            }

            return null;
        }


        /**
         * Returns value (stub)
         * @static
         * @param  mixed $value
         * @return mixed
         */
        public static function ToParameter( &$value ) {
            return $value;
        }


        /**
         * @see ToDateTime()
         * @static
         * @param  mixed  $value
         * @param DateTimeZone|null $zone
         * @return DateTimeWrapper|null
         */
        public static function ToDate( $value, DateTimeZone $zone = null ) {
            return self::ToDateTime( $value, $zone );
        }


        /**
         * @see ToDateTime
         * @static
         * @param  mixed $value
         * @param DateTimeZone|null $zone
         * @return DateTimeWrapper|null
         */
        public static function ToTime( $value, DateTimeZone $zone = null ) {
            return self::ToDateTime( $value, $zone );
        }


        /**
         * Convert value To Type
         *
         * @param mixed  $value
         * @param string $type
         * @return mixed
         */
        public static function ToValue( $value, $type = TYPE_PARAMETER ) {
            switch ( $type ) {
                case TYPE_STRING:
                    return Convert::ToString( $value );
                case TYPE_INTEGER:
                    return Convert::ToInt( $value );
                case TYPE_FLOAT:
                    return Convert::ToFloat( $value );
                case TYPE_BOOLEAN:
                    return Convert::ToBoolean( $value );
                case TYPE_DATE:
                case TYPE_TIME:
                case TYPE_DATETIME:
                    return Convert::ToDateTime( $value );
                case TYPE_ARRAY:
                    return Convert::ToArray( $value );
                case TYPE_OBJECT:
                    return Convert::ToObject( $value );
                case TYPE_PARAMETER:
                    return Convert::ToParameter( $value );
                default:
                    return $value;
            }
        }
    }
?>