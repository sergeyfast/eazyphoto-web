<?php


    /**
     * NavigationType Factory
     *
     * @package %project%
     * @subpackage Common
     *
     * @method static NavigationType[] Get( $search = null, $options = null, $connectionName = NavigationTypeFactory::DefaultConnection ) Get Objects
     * @method static NavigationType   GetById( $id, $search = null, $options = null, $connectionName = NavigationTypeFactory::DefaultConnection ) Get Object By Id
     * @method static NavigationType   GetOne( $search = null, $options = null, $connectionName = NavigationTypeFactory::DefaultConnection ) Get One Object
     * @method static NavigationType   GetFromRequest( $prefix = null, $connectionName = NavigationTypeFactory::DefaultConnection ) Get Object from Request
     */
    class NavigationTypeFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** NavigationType instance mapping  */
        public static $mapping = [
            'class'     => 'NavigationType',
            'table'     => 'navigationTypes',
            'view'      => 'getNavigationTypes',
            'flags'     => [ 'CanCache' => 'CanCache' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'navigationTypeId' => [
                    'name'        => 'navigationTypeId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'title'            => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'alias'            => [
                    'name'        => 'alias',
                    'type'        => TYPE_STRING,
                    'max'         => 32,
                    'nullable'    => 'CheckEmpty',
                ],
                'statusId'         => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
            ],
            'lists'     => [],
            'search'    => [
                '_navigationTypeId' => [
                    'name'       => 'navigationTypeId',
                    'type'       => TYPE_INTEGER,
                    'searchType' => SEARCHTYPE_ARRAY,
                ],
            ],
        ];
    }
