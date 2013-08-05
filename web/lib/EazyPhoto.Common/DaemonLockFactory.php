<?php
    /**
     * DaemonLock Factory
     */
    class DaemonLockFactory implements IFactory {

        /** Default Connection Name */
        const DefaultConnection = null;

        /** DaemonLock instance mapping  */
        public static $mapping = array (
            'class'       => 'DaemonLock'
            , 'table'     => 'daemonLocks'
            , 'view'      => 'getDaemonLocks'
            , 'flags'     => array( 'WithoutTemplates' => 'WithoutTemplates' )
            , 'cacheDeps' => array()
            , 'fields'    => array(
                'daemonLockId' => array(
                    'name'          => 'daemonLockId'
                    , 'type'        => TYPE_INTEGER
                    , 'key'         => true
                    , 'addable'     => false
                )
                ,'title' => array(
                    'name'          => 'title'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'packageName' => array(
                    'name'          => 'packageName'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'methodName' => array(
                    'name'          => 'methodName'
                    , 'type'        => TYPE_STRING
                    , 'max'         => 255
                    , 'nullable'    => 'CheckEmpty'
                )
                ,'runAt' => array(
                    'name'          => 'runAt'
                    , 'type'        => TYPE_DATETIME
                    , 'updatable'   => false
                    , 'addable'     => false
                )
                ,'maxExecutionTime' => array(
                    'name'          => 'maxExecutionTime'
                    , 'type'        => TYPE_TIME
                    , 'nullable'    => 'No'
                )
                ,'isActive' => array(
                    'name'          => 'isActive'
                    , 'type'        => TYPE_BOOLEAN
                    , 'updatable'   => false
                    , 'addable'     => false
                ))
            , 'lists'     => array()
            , 'search'    => array()
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

        /** @return DaemonLock[] */
        public static function Get( $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::Get( $searchArray, self::$mapping, $options, $connectionName );
        }

        /** @return DaemonLock */
        public static function GetById( $id, $searchArray = null, $options = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetById( $id, $searchArray, self::$mapping, $options, $connectionName );
        }
        
        /** @return DaemonLock */
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

        /** @return DaemonLock */
        public static function GetFromRequest( $prefix = null, $connectionName = self::DefaultConnection ) {
            return BaseFactory::GetFromRequest( $prefix, self::$mapping, null, $connectionName );
        }
        
    }
?>