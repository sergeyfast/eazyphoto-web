<?php


    /**
     * ObjectImage Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static ObjectImage[] Get( $search = null, $options = null, $connectionName = ObjectImageFactory::DefaultConnection ) Get Objects
     * @method static ObjectImage   GetById( $id, $search = null, $options = null, $connectionName = ObjectImageFactory::DefaultConnection ) Get Object By Id
     * @method static ObjectImage   GetOne( $search = null, $options = null, $connectionName = ObjectImageFactory::DefaultConnection ) Get One Object
     * @method static ObjectImage   GetFromRequest( $prefix = null, $connectionName = ObjectImageFactory::DefaultConnection ) Get Object from Request
     */
    class ObjectImageFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** ObjectImage instance mapping  */
        public static $mapping = [
            'class'     => 'ObjectImage',
            'table'     => 'objectImages',
            'view'      => 'getObjectImages',
            'flags'     => [ 'WithoutTemplates' => 'WithoutTemplates' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'objectImageId' => [
                    'name'        => 'objectImageId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'objectClass'   => [
                    'name'        => 'objectClass',
                    'type'        => TYPE_STRING,
                    'max'         => 32,
                    'nullable'    => 'CheckEmpty',
                ],
                'objectId'      => [
                    'name'        => 'objectId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'No',
                ],
                'title'         => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                ],
                'orderNumber'   => [
                    'name'        => 'orderNumber',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'No',
                ],
                'smallImageId'  => [
                    'name'        => 'smallImageId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'VfsFile',
                ],
                'bigImageId'    => [
                    'name'        => 'bigImageId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'VfsFile',
                ],
                'statusId'      => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
            ],
            'lists'     => [],
            'search'    => [
                '_objectId' => [
                    'name'       => 'objectId',
                    'type'       => TYPE_INTEGER,
                    'searchType' => SEARCHTYPE_ARRAY,
                ],
            ],
        ];
    }
