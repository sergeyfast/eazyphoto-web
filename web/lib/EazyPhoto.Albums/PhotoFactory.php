<?php
    /**
     * WTF MFD EG 1.6
     * Copyright (c) The 1ADW. All rights reserved.
     */
          
    Package::Load( 'EazyPhoto.Albums' );

    /**
     * Photo Factory
     *
     * @package EazyPhoto
     * @subpackage Albums
     */
    class PhotoFactory implements IFactory {

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Photo instance mapping  */
        public static $mapping = array (
            'class'       => 'Photo'
            , 'table'     => 'photos'
            , 'view'      => 'getPhotos'
            , 'flags'     => array( 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' )
            , 'cacheDeps' => array( 'albums' )
            , 'fields'    => array(
                'photoId' => array(
                    'name'          => 'photoId'
                    , 'type'        => TYPE_INTEGER
                    , 'key'         => true
                )
                ,'albumId' => array(
                    'name'          => 'albumId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'Album'
                )
                ,'originalName' => array(
                    'name'          => 'originalName'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'filename' => array(
                    'name'          => 'filename'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'fileSize' => array(
                    'name'          => 'fileSize'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'No'
                )
                ,'fileSizeHd' => array(
                    'name'          => 'fileSizeHd'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'No'
                )
                ,'orderNumber' => array(
                    'name'          => 'orderNumber'
                    , 'type'        => TYPE_INTEGER
                )
                ,'afterText' => array(
                    'name'          => 'afterText'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 65535
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'title' => array(
                    'name'          => 'title'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'searchType'  => SEARCHTYPE_LIKE
                )
                ,'exif' => array(
                    'name'          => 'exif'
                    , 'type'        => TYPE_ARRAY
                    , 'complexType' => 'json'
                    , 'max'         => 65535
                )
                ,'createdAt' => array(
                    'name'          => 'createdAt'
                    , 'type'        => TYPE_DATETIME
                    , 'updatable'   => false
                    , 'addable'     => false
                )
                ,'photoDate' => array(
                    'name'          => 'photoDate'
                    , 'type'        => TYPE_DATETIME
                )
                ,'statusId' => array(
                    'name'          => 'statusId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'Status'
                ))
            , 'lists'     => array()
            , 'search'    => array(
                '_photoId' => array(
                    'name'         => 'photoId'
                    , 'type'       => TYPE_INTEGER
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

        /** @return Photo[] */
        public static function Get( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Get( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return Photo */
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetById( $id, $searchArray, self::$mapping, $options, $connectionName );
        }
        
        /** @return Photo */
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

        /** @return Photo */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }
        
    }
?>