<?php


    /**
     * MetaDetail Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static MetaDetail[] Get( $search = null, $options = null, $connectionName = MetaDetailFactory::DefaultConnection ) Get Objects
     * @method static MetaDetail   GetById( $id, $search = null, $options = null, $connectionName = MetaDetailFactory::DefaultConnection ) Get Object By Id
     * @method static MetaDetail   GetOne( $search = null, $options = null, $connectionName = MetaDetailFactory::DefaultConnection ) Get One Object
     * @method static MetaDetail   GetFromRequest( $prefix = null, $connectionName = MetaDetailFactory::DefaultConnection ) Get Object from Request
     */
    class MetaDetailFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** MetaDetail instance mapping  */
        public static $mapping = [
            'class'     => 'MetaDetail',
            'table'     => 'metaDetails',
            'view'      => 'getMetaDetails',
            'flags'     => [ 'CanPages' => 'CanPages' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'metaDetailId'    => [
                    'name'        => 'metaDetailId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'url'             => [
                    'name'        => 'url',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'objectClass'     => [
                    'name'        => 'objectClass',
                    'type'        => TYPE_STRING,
                    'max'         => 32,
                ],
                'objectId'        => [
                    'name'        => 'objectId',
                    'type'        => TYPE_INTEGER,
                ],
                'pageTitle'       => [
                    'name'        => 'pageTitle',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'metaKeywords'    => [
                    'name'        => 'metaKeywords',
                    'type'        => TYPE_STRING,
                    'max'         => 1024,
                ],
                'metaDescription' => [
                    'name'        => 'metaDescription',
                    'type'        => TYPE_STRING,
                    'max'         => 1024,
                ],
                'alt'             => [
                    'name'        => 'alt',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'canonicalUrl'    => [
                    'name'        => 'canonicalUrl',
                    'type'        => TYPE_STRING,
                    'max'         => 1024,
                ],
                'statusId'        => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
            ],
            'lists'     => [],
            'search'    => [
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
