<?php


    /**
     * Tag Factory
     *
     * @package EazyPhoto
     * @subpackage Albums
     *
     * @method static Tag[] Get( $search = null, $options = null, $connectionName = TagFactory::DefaultConnection ) Get Objects
     * @method static Tag   GetById( $id, $search = null, $options = null, $connectionName = TagFactory::DefaultConnection ) Get Object By Id
     * @method static Tag   GetOne( $search = null, $options = null, $connectionName = TagFactory::DefaultConnection ) Get One Object
     * @method static Tag   GetFromRequest( $prefix = null, $connectionName = TagFactory::DefaultConnection ) Get Object from Request
     */
    class TagFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Tag instance mapping  */
        public static $mapping = [
            'class'     => 'Tag',
            'table'     => 'tags',
            'view'      => 'getTags',
            'flags'     => [],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'tagId'       => [
                    'name'        => 'tagId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'title'       => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'alias'       => [
                    'name'        => 'alias',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'description' => [
                    'name'        => 'description',
                    'type'        => TYPE_STRING,
                ],
                'orderNumber' => [
                    'name'        => 'orderNumber',
                    'type'        => TYPE_INTEGER,
                ],
                'photoPath'   => [
                    'name'        => 'photoPath',
                    'type'        => TYPE_STRING,
                ],
                'photoId'     => [
                    'name'        => 'photoId',
                    'type'        => TYPE_INTEGER,
                    'foreignKey'  => 'Photo',
                ],
                'parentTagId' => [
                    'name'        => 'parentTagId',
                    'type'        => TYPE_INTEGER,
                    'foreignKey'  => 'Tag',
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
                '!tagId'          => [
                    'name'       => 'tagId',
                    'type'       => TYPE_INTEGER,
                    'searchType' => SEARCHTYPE_NOT_EQUALS,
                ],
                'nullParentTagId' => [
                    'name'       => 'parentTagId',
                    'type'       => TYPE_BOOLEAN,
                    'searchType' => SEARCHTYPE_NULL,
                ],
                'eTitle'          => [
                    'name'       => 'title',
                    'type'       => TYPE_STRING,
                ],
                'nnOrderNumber'   => [
                    'name'       => 'orderNumber',
                    'type'       => TYPE_BOOLEAN,
                    'searchType' => SEARCHTYPE_NOT_NULL,
                ],
            ],
        ];
    }
