<?php
    /**
     * Interface for SQL conversation
     *
     * @package Eaze
     * @subpackage Database
     * @author sergeyfast, max3.05
     */
    interface ISqlConvert {

        /**
         * Create Sql Converter with Connection (static)
         * @abstract
         * @param IConnection $connection
         */
        function __construct( IConnection $connection );

        /**
         * Returns 'null' if value === null or value.
         * @static
         * @param  mixed $value
         * @return string
         */
        static function NullToString( $value );

        /**
         * Converts given argument to sql string.
         *
         * @param mixed $value  Value to convert
         */
        static function ToString( $value );


        /**
         * Converts given argument to sql integer.
         *
         * @param mixed $value  Value to convert
         */
        static function ToInt( $value );


        /**
         * Converts given argument to sql integer.
         *
         * @param mixed $value  Value to convert
         */
        static function ToInteger( $value );


        /**
         * Converts given argument to sql double.
         *
         * @param mixed $value  Value to convert
         */
        static function ToDouble( $value );


        /**
         * Converts given argument to sql float.
         *
         * @param float $value
         */
        static function ToFloat( $value );


        /**
         * Converts given argument to sql boolean.
         *
         * @param mixed $value  Value to convert
         */
        static function ToBoolean( $value );


        /**
         * Converts given argument to sql datetime.
         *
         * @param mixed $value  Value to convert
         * @param string $format
         *
         */
        static function ToDateTime( $value, $format = 'Y-m-d H:i:s' );


        /**
         * Converts given argument to sql date.
         *
         * @param mixed $value  Value to convert
         * @param string $format
         *
         */
        static function ToDate( $value, $format = 'Y-m-d' );


        /**
         * Converts given argument to sql time.
         *
         * @param mixed $value  Value to convert
         * @param string $format
         *
         */
        static function ToTime( $value, $format = 'H:i:s' );


        /**
         * Converts given argument to sql in expression.
         *
         * @param mixed $value  value to convert
         * @param string $type  type of the values in the array.
         */
        static function ToList( $value, $type = TYPE_STRING );


        /**
         * Converts given argument from sql string.
         *
         * @param mixed $value  Value to convert
         */
        static function FromString( $value );


        /**
         * Converts given argument from sql integer.
         *
         * @param mixed $value  Value to convert
         */
        static function FromInt( $value );


        /**
         * Converts given argument from sql integer.
         *
         * @param mixed $value  Value to convert
         */
        static function FromInteger( $value );


        /**
         * Converts given argument from sql double.
         *
         * @param mixed $value  Value to convert
         */
        static function FromDouble( $value );


        /**
         * Converts given argument from sql float.
         *
         * @param mixed $value  Value to convert
         */
        static function FromFloat( $value );


        /**
         * Converts given argument from sql boolean.
         *
         * @param mixed $value  Value to convert
         */
        static function FromBoolean( $value );


        /**
         * Converts given argument from sql datetime
         * @static
         * @param  $value
         * @return DateTimeWrapper
         */
        static function FromDate( $value );


        /**
         * Converts given argument from sql datetime.
         *
         * @param mixed $value  Value to convert
         */
        static function FromDateTime( $value );


        /**
         * Converts given argument from sql parameter.
         *
         * @param mixed $value  Value to convert
         * @param string $type  simple type of the value TYPE_*
         *
         */
        static function FromParameter( $value, $type );

        /**
         * Quote Database Object
         *
         * @param string $field
         */
        static function Quote( $field );
    }

?>