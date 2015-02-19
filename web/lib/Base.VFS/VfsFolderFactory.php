<?php
    use Eaze\Model\BaseFactory;


    /**
     * VfsFolder Factory
     *
     * @package Base
     * @subpackage VFS
     *
     */
    class VfsFolderFactory implements Eaze\Model\IFactory {


        /** Default Connection Name */
        const DefaultConnection = null;

        /** VfsFolder instance mapping  */
        public static $mapping = [
            'class'     => 'VfsFolder',
            'table'     => 'vfsFolders',
            'view'      => 'getVfsFolders',
            'flags'     => [ 'CanCache' => 'CanCache', 'IsTree' => 'IsTree', 'WithoutTemplates' => 'WithoutTemplates' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'folderId'       => [
                    'name'        => 'folderId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'parentFolderId' => [
                    'name'        => 'parentFolderId',
                    'type'        => TYPE_INTEGER,
                    'foreignKey'  => 'VfsFolder',
                ],
                'title'          => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                    'searchType'   => SEARCHTYPE_ILIKE,
                ],
                'isFavorite'     => [
                    'name'        => 'isFavorite',
                    'type'        => TYPE_BOOLEAN,
                ],
                'createdAt'      => [
                    'name'        => 'createdAt',
                    'type'        => TYPE_DATETIME,
                    'updatable'   => false,
                    'addable'     => false,
                ],
                'statusId'       => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
            ],
            'lists'     => [],
            'search'    => [
                '_id'        => [
                    'name'       => 'folderId',
                    'type'       => TYPE_INTEGER,
                    'searchType'   => SEARCHTYPE_ARRAY,
                ],
                'exactTitle' => [
                    'name'       => 'title',
                    'type'       => TYPE_STRING,
                ],
            ],
        ];

        /** @return array */
        public static function Validate( $object, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Validate( $object, self::$mapping, $options, $connectionName );
        }

        /** @return array */
        public static function ValidateSearch( $search, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::ValidateSearch( $search, self::$mapping, $options, $connectionName );
        }

        /** @return bool|array */
        public static function Update( $object, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Update( $object, self::$mapping, $options, $connectionName );
        }

        /** @return bool */
        public static function UpdateRange( $objects, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::UpdateRange( $objects, self::$mapping, $options, $connectionName );
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

        public static function GetCurrentId( $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetCurrentId( self::$mapping, $connectionName );
        }

        public static function Delete( $object, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::Delete( $object, self::$mapping, $connectionName );
        }

        public static function DeleteByMask( $searchArray, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::DeleteByMask( $searchArray, self::$mapping, $connectionName );
        }

        public static function PhysicalDelete( $object, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::PhysicalDelete( $object, self::$mapping, $connectionName );
        }

        public static function LogicalDelete( $object, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::LogicalDelete( $object, self::$mapping, $connectionName );
        }

        /** @return VfsFolder */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }

        /// Base Tree Operations.
        public static function Move( $object, $destination, $connectionName = self::DefaultConnection, $mode = TREEMODE_LTREE ) {
            return BaseTreeFactory::Move( $object, $destination, self::$mapping, $connectionName, $mode = TREEMODE_LTREE );
        }

        public static function Copy( $object, $destination, $connectionName = self::DefaultConnection, $mode = TREEMODE_LTREE ) {
            return BaseTreeFactory::Copy( $object, $destination, self::$mapping, $connectionName, $mode = TREEMODE_LTREE );
        }

        public static function Add( $object, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::Add( $object, self::$mapping, null, $connectionName );
        }

        public static function AddRange( $objects, $options = null, $connectionName = self::DefaultConnection ) {
            // TODO: Implement AddRange() method.
        }

        public static function Get( $searchArray = null, $options = null, $object = null, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::Get( $searchArray, $options, $object, self::$mapping, $connectionName );
        }

        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::GetById( $id, $searchArray, $options, null, self::$mapping, $connectionName );
        }

        public static function GetOne( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::GetOne( $searchArray, $options, self::$mapping, $connectionName );
        }

        public static function Count( $searchArray, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseTreeFactory::Count( $searchArray, self::$mapping, $options, $connectionName );
        }

        public static function GetChildren( $object, $searchArray, $options = null, $level = null, $connectionName = self::DefaultConnection, $mode = TREEMODE_LTREE ) {
            $level = empty( $level ) ? 1 : $level;
            return BaseTreeFactory::GetChildren( $object, $searchArray, $options, $level, self::$mapping, $connectionName, $mode );
        }

        public static function GetBranch( $object, $connectionName = self::DefaultConnection, $mode = TREEMODE_LTREE ) {
            return BaseTreeFactory::GetBranch( $object, self::$mapping, $connectionName, $mode );
        }
    }
