<?php


    /**
     * DaemonLock Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static DaemonLock[] Get( $search = null, $options = null, $connectionName = DaemonLockFactory::DefaultConnection ) Get Objects
     * @method static DaemonLock   GetById( $id, $search = null, $options = null, $connectionName = DaemonLockFactory::DefaultConnection ) Get Object By Id
     * @method static DaemonLock   GetOne( $search = null, $options = null, $connectionName = DaemonLockFactory::DefaultConnection ) Get One Object
     * @method static DaemonLock   GetFromRequest( $prefix = null, $connectionName = DaemonLockFactory::DefaultConnection ) Get Object from Request
     */
    class DaemonLockFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** DaemonLock instance mapping  */
        public static $mapping = [
            'class'     => 'DaemonLock',
            'table'     => 'daemonLocks',
            'view'      => 'getDaemonLocks',
            'flags'     => [ 'ReadOnlyTemplates' => 'ReadOnlyTemplates' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'daemonLockId'     => [
                    'name'        => 'daemonLockId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                    'addable'     => false,
                ],
                'title'            => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'packageName'      => [
                    'name'        => 'packageName',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'methodName'       => [
                    'name'        => 'methodName',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'runAt'            => [
                    'name'        => 'runAt',
                    'type'        => TYPE_DATETIME,
                    'updatable'   => false,
                    'addable'     => false,
                ],
                'maxExecutionTime' => [
                    'name'        => 'maxExecutionTime',
                    'type'        => TYPE_TIME,
                    'nullable'    => 'No',
                ],
                'isActive'         => [
                    'name'        => 'isActive',
                    'type'        => TYPE_BOOLEAN,
                    'updatable'   => false,
                    'addable'     => false,
                ],
            ],
            'lists'     => [],
            'search'    => [],
        ];
    }
