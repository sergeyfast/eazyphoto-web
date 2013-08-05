<?php
    /**
     * VfsFile Factory
     *
     * @package Base
     * @subpackage VFS
     */
    class VfsFileFactory implements IFactory {

        /** Default Connection Name */
        const DefaultConnection = null;

        /** VfsFile instance mapping  */
        public static $mapping = array (
            'class'       => 'VfsFile'
            , 'table'     => 'vfsFiles'
            , 'view'      => 'getVfsFiles'
            , 'flags'     => array( 'CanPages' => 'CanPages', 'CanCache' => 'CanCache', 'WithoutTemplates' => 'WithoutTemplates' )
            , 'cacheDeps' => array( 'vfsFolders' )
            , 'fields'    => array(
                'fileId' => array(
                    'name'          => 'fileId'
                    , 'type'        => TYPE_INTEGER
                    , 'key'         => true
                )
                ,'folderId' => array(
                    'name'          => 'folderId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'VfsFolder'
                )
                ,'title' => array(
                    'name'          => 'title'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'path' => array(
                    'name'          => 'path'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'params' => array(
                    'name'          => 'params'
                    , 'type'        => TYPE_ARRAY
                    , 'complexType' => 'json'
                )
                ,'isFavorite' => array(
                    'name'          => 'isFavorite'
                    , 'type'        => TYPE_BOOLEAN
                )
                ,'mimeType' => array(
                    'name'          => 'mimeType'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'fileSize' => array(
                    'name'          => 'fileSize'
                    , 'type'        => TYPE_INTEGER
                )
                ,'fileExists' => array(
                    'name'          => 'fileExists'
                    , 'type'        => TYPE_BOOLEAN
                    , 'nullable'    => 'No'
                )
                ,'statusId' => array(
                    'name'          => 'statusId'
                    , 'type'        => TYPE_INTEGER
                    , 'nullable'    => 'CheckEmpty'
                    , 'foreignKey'  => 'Status'
                )
                ,'createdAt' => array(
                    'name'          => 'createdAt'
                    , 'type'        => TYPE_DATETIME
                    , 'updatable'   => false
                    , 'addable'     => false
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
                ,'title%' => array(
                    'name'         => 'title'
                    , 'type'       => TYPE_STRING
                    , 'searchType' => SEARCHTYPE_RIGHT_LIKE
                )
                ,'_fileId' => array(
                    'name'         => 'fileId'
                    , 'type'       => TYPE_INTEGER
                    , 'searchType' => SEARCHTYPE_ARRAY
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

        /** @return VfsFile[] */
        public static function Get( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Get( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return VfsFile */
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetById( $id, $searchArray, self::$mapping, $options, $connectionName );
        }
        
        /** @return VfsFile */
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

        /** @return VfsFile */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }
        
    }
?>