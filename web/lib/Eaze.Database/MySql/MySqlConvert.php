<?php
    /**
     * MySQL Type Converter
     * 
     * @package Eaze
     * @subpackage MySql
     * @author max3.05, sergeyfast
     */
    class MySqlConvert implements ISqlConvert {

        /**
         * Connection
         * @var MySqlConnection
         */
        private static $connection;

        /**
         * Create MySqlConvert
         * @param IConnection $connection
         */
        public function __construct( IConnection $connection ) {
            if ( empty( self::$connection ) || self::$connection != $connection ) {
                self::$connection = $connection;
            }
        }


        /**
         * Convert Null Value to String
         * @static
         * @param  $value
         * @return string
         */
        public static function NullToString( $value ) {
            if ( $value === null ) {
                return 'null';
            }
            
            return $value;   
        }
        
        
        /**
         * Converts given argument to sql string.
         * @static
         * @param mixed $value  Value to convert
         * @return string
         */
        public static function ToString( $value ) {
            static $firstTry;

            $value = Convert::ToString( $value );
            if ( $value === null ) {
                return 'null';
            }

            if ( $firstTry === null && !empty( self::$connection ) && !self::$connection->IsOpened() ) {
                $firstTry = !self::$connection->Open();
            }

            if ( empty( self::$connection ) || self::$connection->GetResource() === null ) {
                Logger::Error( 'Connection was not found ' );
                return null;
            }

            $sqlString = "'" . mysql_real_escape_string( $value, self::$connection->GetResource() )  . "'";
           
            return $sqlString;
        }
        
        
        /**
         * Converts given argument to sql integer.
         *
         * @param mixed $value  Value to convert
         * @return string
         */
        public static function ToInt( $value ) {
            return self::NullToString( Convert::ToInteger( $value ) );
        }
        
        
        /**
         * Converts given argument to sql integer.
         *
         * @param mixed $value  Value to convert
         * @return string
         */
        public static function ToInteger( $value ) {
            return self::NullToString( Convert::ToInteger( $value ) );
        }
        
        
        /**
         * Converts given argument to sql double.
         *
         * @param mixed $value  Value to convert
         * @return string
         */
        public static function ToDouble( $value ) {
            return self::NullToString( Convert::ToDouble( $value ) );
        }
        
        
        /**
         * Converts given argument to sql float.
         *
         * @param float $value
         * @return string
         */
        public static function ToFloat( $value ) {
            return self::NullToString( Convert::ToFloat( $value ) );
        }
        
        
        /**
         * Converts given argument to sql boolean.
         *
         * @param mixed $value  Value to convert
         * @return string
         */
        public static function ToBoolean( $value ) {
            if ( $value === true || $value == 'true' ) {
                return '1';
            } else if ( $value === false || $value == 'false' ) {
                return '0';
            }

            return 'null';
        }


        /**
         * Converts given argument to sql datetime.
         *
         * @param mixed $value  Value to convert
         * @param string $format
         * @return string
         */
        public static function ToDateTime( $value, $format = 'Y-m-d H:i:s' ) {
            $value = Convert::ToDateTime( $value );
            if ( $value == null ) {
                return 'null';
            }

            return self::ToString( $value->format( $format ) );
        }


        /**
         * Converts given argument to sql date.
         *
         * @param mixed $value  Value to convert
         * @param string $format
         * @return string
         */
        public static function ToDate( $value, $format = 'Y-m-d' ) {
            return self::ToDateTime( $value, $format);
        }


        /**
         * Converts given argument to sql time.
         *
         * @param mixed $value  Value to convert
         * @param string $format
         * @return string
         */
        public static function ToTime( $value, $format = 'G:i:s' ){
            return self::ToDateTime( $value, $format);
        }        
        

        /**
         * Converts given argument from sql string.
         *
         * @param mixed $value  Value to convert
         * @return string
         */
        public static function FromString( $value ) {
            return Convert::ToString( $value );
        }
        
        
        /**
         * Converts given argument from sql integer.
         *
         * @param mixed $value  Value to convert
         * @return integer
         */
        public static function FromInt( $value ) {
            return Convert::ToInteger( $value );
        }
        
        
        /**
         * Converts given argument from sql integer.
         *
         * @param mixed $value  Value to convert
         * @return integer
         */
        public static function FromInteger( $value ) {
            return Convert::ToInteger( $value );
        }
        
        
        /**
         * Converts given argument from sql double.
         *
         * @param mixed $value  Value to convert
         * @return double
         */
        public static function FromDouble( $value ) {
            return Convert::ToDouble( $value );
        }
        
        
        /**
         * Converts given argument from sql float.
         *
         * @param mixed $value  Value to convert
         * @return float
         */
        public static function FromFloat( $value ) {
            return Convert::ToFloat( $value );
        }
        
        
        /**
         * Converts given argument from sql boolean.
         *
         * @param mixed $value  Value to convert
         * @return boolean
         */
        public static function FromBoolean( $value ) {
            $value = Convert::ToInteger( $value );
            
            switch ( $value ) {
                case 1:
                case '1':
                    return true;
                case 0:
                case '0':
                    return false;
                default:
                    return null;
            }
        }

        
        /**
         * Converts given argument from sql Datetime.
         *
         * @param mixed $value  Value to convert
         * @return Datetime
         */
        public static function FromDateTime( $value ) {
            return Convert::ToDateTime( $value );
        }
        
		/**
         * Converts given argument from sql Datetime.
         *
         * @param mixed $value  Value to convert
         * @return Datetime
         */
        public static function FromDate( $value ) {
            return Convert::ToDateTime( $value );
        }


        /**
         * Converts given argument from sql parameters.
         *
         * @param mixed $value  Value to convert
         * @param $type
         * @return mixed
         */
        public static function FromParameter( $value, $type ) {
            switch ( $type ) {
                case TYPE_INTEGER:
                    return self::FromInteger( $value  );
                case TYPE_FLOAT:
                    return self::FromFloat( $value );
                case TYPE_BOOLEAN:
                    return self::FromBoolean( $value );
                case TYPE_STRING:
                    return self::FromString( $value );
                case TYPE_DATETIME:
                case TYPE_DATE:
                case TYPE_TIME:
                    return self::FromDateTime( $value );
                case TYPE_LTREE:
                    return self::FromString( $value );
                default:
                    Logger::Error( 'Cannot call converter for %s of class MySqlConvert', $type);
                    return null;
            }
        }


        /**
         * Converts given argument to sql in expression.
         *
         * @param mixed $value  value to convert
         * @param string $type  type of the values in the array.
         * @return string
         */
        public static function ToList( $value, $type = TYPE_STRING ) {
            $method = 'To' . $type;

            if ( !is_callable( array( __CLASS__ , $method ) ) ) {
                Logger::Error( 'Call to undefined method %s', $method );
            }

            $items = Convert::ToArray( $value );
            $items = array_map( array( __CLASS__ , $method ), $items) ;

            return '(' . implode( ',', $items ) . ')';
        }


        /**
         * Quote Database Object
         *
         * @param string $field
         * @return string
         */
        public static function Quote( $field ) {
            return '`' . $field . '`';
        }
    }
?>