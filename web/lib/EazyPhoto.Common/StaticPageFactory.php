<?php


    /**
     * StaticPage Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static StaticPage[] Get( $search = null, $options = null, $connectionName = StaticPageFactory::DefaultConnection ) Get Objects
     * @method static StaticPage   GetById( $id, $search = null, $options = null, $connectionName = StaticPageFactory::DefaultConnection ) Get Object By Id
     * @method static StaticPage   GetOne( $search = null, $options = null, $connectionName = StaticPageFactory::DefaultConnection ) Get One Object
     * @method static StaticPage   GetFromRequest( $prefix = null, $connectionName = StaticPageFactory::DefaultConnection ) Get Object from Request
     */
    class StaticPageFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** StaticPage instance mapping  */
        public static $mapping = [
            'class'     => 'StaticPage',
            'table'     => 'staticPages',
            'view'      => 'getStaticPages',
            'flags'     => [ 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'staticPageId'       => [
                    'name'        => 'staticPageId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'title'              => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'url'                => [
                    'name'        => 'url',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'content'            => [
                    'name'        => 'content',
                    'type'        => TYPE_STRING,
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'orderNumber'        => [
                    'name'        => 'orderNumber',
                    'type'        => TYPE_INTEGER,
                ],
                'parentStaticPageId' => [
                    'name'        => 'parentStaticPageId',
                    'type'        => TYPE_INTEGER,
                    'foreignKey'  => 'StaticPage',
                ],
                'statusId'           => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
                'nodes'              => [
                    'name'        => 'nodes',
                    'type'        => TYPE_ARRAY,
                    'updatable'   => false,
                    'addable'     => false,
                ],
                'images'             => [
                    'name'        => 'images',
                    'type'        => TYPE_ARRAY,
                    'updatable'   => false,
                    'addable'     => false,
                ],
            ],
            'lists'     => [],
            'search'    => [
                'page'          => [
                    'name'       => 'page',
                    'type'       => TYPE_INTEGER,
                    'default'    => 0,
                ],
                'pageSize'      => [
                    'name'       => 'pageSize',
                    'type'       => TYPE_INTEGER,
                    'default'    => 25,
                ],
                '!staticPageId' => [
                    'name'       => 'staticPageId',
                    'type'       => TYPE_INTEGER,
                    'searchType' => SEARCHTYPE_NOT_EQUALS,
                ],
            ],
        ];
    }
