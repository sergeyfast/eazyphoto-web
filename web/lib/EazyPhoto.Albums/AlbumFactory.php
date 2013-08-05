<?php
    /**
     * WTF MFD EG 1.6
     * Copyright (c) The 1ADW. All rights reserved.
     */
          
    Package::Load( 'EazyPhoto.Albums' );

    /**
     * Album Factory
     *
     * @package EazyPhoto
     * @subpackage Albums
     */
    class AlbumFactory implements IFactory {

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Album instance mapping  */
        public static $mapping = array (
            'class'       => 'Album'
            , 'table'     => 'albums'
            , 'view'      => 'getAlbums'
            , 'flags'     => array( 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' )
            , 'cacheDeps' => array( 'users' )
            , 'fields'    => array(
                'albumId' => array(
                    'name'          => 'albumId'
                    , 'type'        => TYPE_INTEGER
                    , 'key'         => true
                )
                ,'title' => array(
                    'name'          => 'title'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'description' => array(
                    'name'          => 'description'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 4096
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'alias' => array(
                    'name'          => 'alias'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'isPrivate' => array(
                    'name'          => 'isPrivate'
                    , 'type'        => TYPE_BOOLEAN
                    , 'nullable'    => 'No'
                )
                ,'startDate' => array(
                    'name'          => 'startDate'
                    , 'type'        => TYPE_DATE
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'endDate' => array(
                    'name'          => 'endDate'
                    , 'type'        => TYPE_DATE
                )
                ,'orderNumber' => array(
                    'name'          => 'orderNumber'
                    , 'type'        => TYPE_INTEGER
                )
                ,'folderPath' => array(
                    'name'          => 'folderPath'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'roSecret' => array(
                    'name'          => 'roSecret'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 1024
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'roSecretHd' => array(
                    'name'          => 'roSecretHd'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 1024
                )
                ,'deleteOriginalsAfter' => array(
                    'name'          => 'deleteOriginalsAfter'
                    , 'type'        => TYPE_INTEGER
                )
                ,'isDescSort' => array(
                    'name'          => 'isDescSort'
                    , 'type'        => TYPE_BOOLEAN
                    , 'nullable'    => 'No'
                )
                ,'createdAt' => array(
                    'name'          => 'createdAt'
                    , 'type'        => TYPE_DATETIME
                    , 'updatable'   => false
                    , 'addable'     => false
                )
                ,'modifiedAt' => array(
                    'name'          => 'modifiedAt'
                    , 'type'        => TYPE_DATETIME
                    , 'nullable'    => 'No'
                )
                ,'userId' => array(
                    'name'          => 'userId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'User'
                )
                ,'metaInfo' => array(
                    'name'          => 'metaInfo'
                    , 'type'        => TYPE_ARRAY
                    , 'complexType' => 'json'
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
                'page' => array(
                    'name'         => 'page'
                    , 'type'       => TYPE_INTEGER
                    , 'default'    => 0
                )
                ,'pageSize' => array(
                    'name'         => 'pageSize'
                    , 'type'       => TYPE_INTEGER
                    , 'default'    => 25
                )
                ,'geStartDate' => array(
                    'name'         => 'startDate'
                    , 'type'       => TYPE_DATE
                    , 'searchType' => SEARCHTYPE_GE
                )
                ,'leStartDate' => array(
                    'name'         => 'startDate'
                    , 'type'       => TYPE_DATE
                    , 'searchType' => SEARCHTYPE_LE
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

        /** @return Album[] */
        public static function Get( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Get( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return Album */
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetById( $id, $searchArray, self::$mapping, $options, $connectionName );
        }
        
        /** @return Album */
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

        /** @return Album */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }
        
    }
?>