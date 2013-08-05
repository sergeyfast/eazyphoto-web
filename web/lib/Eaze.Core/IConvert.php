<?php
    define( 'TYPE_STRING',    'string' );
    define( 'TYPE_INTEGER',   'integer' );
    define( 'TYPE_FLOAT',     'float' );
    define( 'TYPE_BOOLEAN',   'boolean' );
    define( 'TYPE_ARRAY',     'array' );
    define( 'TYPE_OBJECT',    'object' );
    define( 'TYPE_RESOURCE',  'resource' );
    define( 'TYPE_PARAMETER', 'parameter' );
    define( 'TYPE_DATETIME',  'dateTime' );
    define( 'TYPE_TIME',      'time' );
    define( 'TYPE_DATE',      'date' );
    define( 'TYPE_LTREE',     'ltree' );
    
    define( 'MODE_GET',       'get' );
    define( 'MODE_SET',       'set' );
    
    interface IConvert {
    
        static function ToString( $value );
        static function ToInt( $value );
        static function ToInteger( $value );
        static function ToDouble( $value );
        static function ToFloat( $value );
        static function ToBoolean( $value );
        static function ToArray( $value );
        static function ToObject( $value );
        static function ToDateTime( $value, DateTimeZone $zone = null );
        static function ToDate( $value, DateTimeZone $zone = null );
        static function ToTime( $value, DateTimeZone $zone = null );
        static function ToParameter( &$value );
    }
?>