<?php
    define( 'SEARCHTYPE_EQUALS',        'equals' );
    define( 'SEARCHTYPE_NOT_EQUALS',    'notEquals' );
    define( 'SEARCHTYPE_NULL',          'null' );
    define( 'SEARCHTYPE_NOT_NULL',      'notNull' );
    define( 'SEARCHTYPE_GE',            '>=' );
    define( 'SEARCHTYPE_LE',            '<=' );
    define( 'SEARCHTYPE_G',             '>' );
    define( 'SEARCHTYPE_L',             '<' );
    define( 'SEARCHTYPE_LEFT_LIKE',     'l_like' );
    define( 'SEARCHTYPE_LEFT_ILIKE',    'l_ilike' );
    define( 'SEARCHTYPE_RIGHT_LIKE',    'r_like' );
    define( 'SEARCHTYPE_RIGHT_ILIKE',   'r_ilike' );
    define( 'SEARCHTYPE_LIKE',          'like' );
    define( 'SEARCHTYPE_ILIKE',         'ilike' );
    define( 'SEARCHTYPE_ARRAY',         'inArray' );
    define( 'SEARCHTYPE_NOT_INARRAY',   'notInArray' );

    define( 'OPTION_WITH_PARENT',       'withParent' );
    define( 'OPTION_WITH_CHILDREN',     'withChildren' );
    define( 'OPTION_LEVEL_MIN',         'level_min' );
    define( 'OPTION_LEVEL_MAX',         'level_max' );

    interface  IFactory {
        public static function Validate( $object, $options = null, $connectionName = null );
        public static function ValidateSearch( $search, $options = null, $connectionName = null );
        public static function Add( $object, $options = null, $connectionName = null );
        public static function AddRange( $objects, $options = null, $connectionName = null );
        public static function Update( $object, $options = null, $connectionName = null );
        public static function UpdateRange( $objects, $options = null, $connectionName = null );
        public static function UpdateByMask( $object, $changes, $searchArray = null, $connectionName = null );
        public static function SaveArray( $objects, $originalObjects = null, $connectionName = null );
        public static function CanPages();
        public static function Count( $searchArray, $options = null, $connectionName = null );


        /**
         * @param array $searchArray
         * @param array $options
         * @param array $connectionName
         * @return array objects
         */
        public static function Get( $searchArray = null, $options = null, $connectionName = null );
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = null );
        public static function GetOne( $searchArray = null, $options = null, $connectionName = null );
        public static function GetCurrentId( $connectionName = null );
        public static function Delete( $object, $connectionName = null );
        public static function DeleteByMask( $searchArray, $connectionName = null );
        public static function PhysicalDelete( $object, $connectionName = null );
        public static function LogicalDelete( $object, $connectionName = null );
        public static function GetFromRequest( $prefix = null, $connectionName = null );
    }
?>