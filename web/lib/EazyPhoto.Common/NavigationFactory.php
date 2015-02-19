<?php


    /**
     * Navigation Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static Navigation[] Get( $search = null, $options = null, $connectionName = NavigationFactory::DefaultConnection ) Get Objects
     * @method static Navigation   GetById( $id, $search = null, $options = null, $connectionName = NavigationFactory::DefaultConnection ) Get Object By Id
     * @method static Navigation   GetOne( $search = null, $options = null, $connectionName = NavigationFactory::DefaultConnection ) Get One Object
     * @method static Navigation   GetFromRequest( $prefix = null, $connectionName = NavigationFactory::DefaultConnection ) Get Object from Request
     */
    class NavigationFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Navigation instance mapping  */
        public static $mapping = [
            'class'     => 'Navigation',
            'table'     => 'navigations',
            'view'      => 'getNavigations',
            'flags'     => [ 'CanCache' => 'CanCache', 'IsLocked' => 'IsLocked' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'navigationId'     => [
                    'name'        => 'navigationId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'title'            => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'orderNumber'      => [
                    'name'        => 'orderNumber',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'No',
                ],
                'navigationTypeId' => [
                    'name'        => 'navigationTypeId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'NavigationType',
                ],
                'staticPageId'     => [
                    'name'        => 'staticPageId',
                    'type'        => TYPE_INTEGER,
                    'foreignKey'  => 'StaticPage',
                ],
                'url'              => [
                    'name'        => 'url',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'statusId'         => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
                'nodes'            => [
                    'name'        => 'nodes',
                    'type'        => TYPE_ARRAY,
                    'updatable'   => false,
                    'addable'     => false,
                ],
                'params'           => [
                    'name'        => 'params',
                    'type'        => TYPE_ARRAY,
                    'complexType' => 'json',
                ],
            ],
            'lists'     => [],
            'search'    => [
                'navigationType.alias' => [
                    'name'       => 'navigationType.alias',
                    'type'       => TYPE_STRING,
                ],
            ],
        ];
    }
