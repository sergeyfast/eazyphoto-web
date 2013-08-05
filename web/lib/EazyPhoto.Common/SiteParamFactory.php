<?php
    /**
     * SiteParam Factory
     */
    class SiteParamFactory implements IFactory {

        /** Default Connection Name */
        const DefaultConnection = null;

        /** SiteParam instance mapping  */
        public static $mapping = array (
            'class'       => 'SiteParam'
            , 'table'     => 'siteParams'
            , 'view'      => 'getSiteParams'
            , 'flags'     => array( 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' )
            , 'cacheDeps' => array()
            , 'fields'    => array(
                'siteParamId' => array(
                    'name'          => 'siteParamId'
                    , 'type'        => TYPE_INTEGER
                    , 'key'         => true
                )
                ,'alias' => array(
                    'name'          => 'alias'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 32
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'value' => array(
                    'name'          => 'value'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'description' => array(
                    'name'          => 'description'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                )
                ,'statusId' => array(
                    'name'          => 'statusId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'Status'
                ))
            , 'lists'     => array()
            , 'search'    => array(
                '_alias' => array(
                    'name'         => 'alias'
                    , 'type'       => TYPE_STRING
                    , 'searchType' => SEARCHTYPE_ARRAY
                )
                ,'page' => array(
                    'name'         => 'page'
                    , 'type'       => TYPE_INTEGER
                    , 'default'    => 0
                )
                ,'pageSize' => array(
                    'name'         => 'pageSize'
                    , 'type'       => TYPE_INTEGER
                    , 'default'    => 25
                ))
        );
        
        /** @return array */
        public static function Validate( $object, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Validate( $object, self::$mapping, $options, $connectionName );
        }

        /** @return array */
        public static function ValidateSearch( $search, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::ValidateSearch( $search, self::$mapping, $options, $connectionName );
        }
        
        /** @return bool|array */
        public static function UpdateByMask( $object, $changes, $searchArray = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::UpdateByMask( $object, $changes, $searchArray, self::$mapping, $connectionName );
        }

        public static function SaveArray( $objects, $originalObjects = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::SaveArray( $objects, $originalObjects, self::$mapping, $connectionName );
        }

        public static function CanPages() {
            return BaseFactory::CanPages( self::$mapping );
        }        
        
        /** @return bool|array */
        public static function Add( $object, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Add( $object, self::$mapping, $options, $connectionName );
        }
        
        /** @return bool */
        public static function AddRange( $objects, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::AddRange( $objects, self::$mapping, $options, $connectionName );
        }

        /** @return bool|array */
        public static function Update( $object, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Update( $object, self::$mapping, $options, $connectionName );
        }

        /** @return bool */
        public static function UpdateRange( $objects, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::UpdateRange( $objects, self::$mapping, $options, $connectionName );
        }

        public static function Count( $searchArray, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Count( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return SiteParam[] */
        public static function Get( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Get( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return SiteParam */
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetById( $id, $searchArray, self::$mapping, $options, $connectionName );
        }
        
        /** @return SiteParam */
        public static function GetOne( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetOne( $searchArray, self::$mapping, $options, $connectionName );
        }
        
        public static function GetCurrentId( $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetCurrentId( self::$mapping, $connectionName );
        }

        public static function Delete( $object, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Delete( $object, self::$mapping, $connectionName );
        }

        public static function DeleteByMask( $searchArray, $connectionName = self::DefaultConnection ) {
            return BaseFactory::DeleteByMask( $searchArray, self::$mapping, $connectionName );
        }

        public static function PhysicalDelete( $object, $connectionName = self::DefaultConnection ) {
            return BaseFactory::PhysicalDelete( $object, self::$mapping, $connectionName );
        }

        public static function LogicalDelete( $object, $connectionName = self::DefaultConnection ) {
            return BaseFactory::LogicalDelete( $object, self::$mapping, $connectionName );
        }

        /** @return SiteParam */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }
        
    }
?>