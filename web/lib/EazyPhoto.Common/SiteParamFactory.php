<?php


    /**
     * SiteParam Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static SiteParam[] Get( $search = null, $options = null, $connectionName = SiteParamFactory::DefaultConnection ) Get Objects
     * @method static SiteParam   GetById( $id, $search = null, $options = null, $connectionName = SiteParamFactory::DefaultConnection ) Get Object By Id
     * @method static SiteParam   GetOne( $search = null, $options = null, $connectionName = SiteParamFactory::DefaultConnection ) Get One Object
     * @method static SiteParam   GetFromRequest( $prefix = null, $connectionName = SiteParamFactory::DefaultConnection ) Get Object from Request
     */
    class SiteParamFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** SiteParam instance mapping  */
        public static $mapping = [
            'class'     => 'SiteParam',
            'table'     => 'siteParams',
            'view'      => 'getSiteParams',
            'flags'     => [ 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'siteParamId' => [
                    'name'        => 'siteParamId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'alias'       => [
                    'name'        => 'alias',
                    'type'        => TYPE_STRING,
                    'max'         => 32,
                    'nullable'    => 'CheckEmpty',
                ],
                'value'       => [
                    'name'        => 'value',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'description' => [
                    'name'        => 'description',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'statusId'    => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
            ],
            'lists'     => [],
            'search'    => [
                '_alias'   => [
                    'name'       => 'alias',
                    'type'       => TYPE_STRING,
                    'searchType' => SEARCHTYPE_ARRAY,
                ],
                'page'     => [
                    'name'       => 'page',
                    'type'       => TYPE_INTEGER,
                    'default'    => 0,
                ],
                'pageSize' => [
                    'name'       => 'pageSize',
                    'type'       => TYPE_INTEGER,
                    'default'    => 25,
                ],
            ],
        ];
    }
