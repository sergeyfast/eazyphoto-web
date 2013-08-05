<?php
    /**
     * MetaDetail Factory
     */
    class MetaDetailFactory implements IFactory {

        /** Default Connection Name */
        const DefaultConnection = null;

        /** MetaDetail instance mapping  */
        public static $mapping = array (
            'class'       => 'MetaDetail'
            , 'table'     => 'metaDetails'
            , 'view'      => 'getMetaDetails'
            , 'flags'     => array( 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' )
            , 'cacheDeps' => array()
            , 'fields'    => array(
                'metaDetailId' => array(
                    'name'          => 'metaDetailId'
                    , 'type'        => TYPE_INTEGER
                    , 'key'         => true
                )
                ,'url' => array(
                    'name'          => 'url'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'pageTitle' => array(
                    'name'          => 'pageTitle'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'metaKeywords' => array(
                    'name'          => 'metaKeywords'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 1024
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'metaDescription' => array(
                    'name'          => 'metaDescription'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 1024
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'alt' => array(
                    'name'          => 'alt'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'isInheritable' => array(
                    'name'          => 'isInheritable'
                    , 'type'        => TYPE_BOOLEAN
                    , 'nullable'    => 'No'
                )
                ,'statusId' => array(
                    'name'          => 'statusId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'Status'
                ))
            , 'lists'     => array()
            , 'search'    => array(
                'startUrl' => array(
                    'name'         => 'url'
                    , 'type'       => TYPE_STRING
                    , 'searchType' => SEARCHTYPE_RIGHT_ILIKE
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

        /** @return MetaDetail[] */
        public static function Get( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Get( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return MetaDetail */
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetById( $id, $searchArray, self::$mapping, $options, $connectionName );
        }
        
        /** @return MetaDetail */
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

        /** @return MetaDetail */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }
        
    }
?>