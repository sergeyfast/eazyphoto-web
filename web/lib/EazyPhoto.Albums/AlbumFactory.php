<?php


    /**
     * Album Factory
     *
     * @package EazyPhoto
     * @subpackage Albums
     *
     * @method static Album[] Get( $search = null, $options = null, $connectionName = AlbumFactory::DefaultConnection ) Get Objects
     * @method static Album   GetById( $id, $search = null, $options = null, $connectionName = AlbumFactory::DefaultConnection ) Get Object By Id
     * @method static Album   GetOne( $search = null, $options = null, $connectionName = AlbumFactory::DefaultConnection ) Get One Object
     * @method static Album   GetFromRequest( $prefix = null, $connectionName = AlbumFactory::DefaultConnection ) Get Object from Request
     */
    class AlbumFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Album instance mapping  */
        public static $mapping = [
            'class'     => 'Album',
            'table'     => 'albums',
            'view'      => 'getAlbums',
            'flags'     => [ 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'albumId'              => [
                    'name'        => 'albumId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'title'                => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'description'          => [
                    'name'        => 'description',
                    'type'        => TYPE_STRING,
                    'max'         => 4096,
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'alias'                => [
                    'name'        => 'alias',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'isPrivate'            => [
                    'name'        => 'isPrivate',
                    'type'        => TYPE_BOOLEAN,
                    'nullable'    => 'No',
                ],
                'startDate'            => [
                    'name'        => 'startDate',
                    'type'        => TYPE_DATE,
                    'nullable'    => 'CheckEmpty',
                ],
                'endDate'              => [
                    'name'        => 'endDate',
                    'type'        => TYPE_DATE,
                ],
                'orderNumber'          => [
                    'name'        => 'orderNumber',
                    'type'        => TYPE_INTEGER,
                ],
                'folderPath'           => [
                    'name'        => 'folderPath',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'roSecret'             => [
                    'name'        => 'roSecret',
                    'type'        => TYPE_STRING,
                    'max'         => 1024,
                    'nullable'    => 'CheckEmpty',
                ],
                'roSecretHd'           => [
                    'name'        => 'roSecretHd',
                    'type'        => TYPE_STRING,
                    'max'         => 1024,
                ],
                'deleteOriginalsAfter' => [
                    'name'        => 'deleteOriginalsAfter',
                    'type'        => TYPE_INTEGER,
                ],
                'isDescSort'           => [
                    'name'        => 'isDescSort',
                    'type'        => TYPE_BOOLEAN,
                    'nullable'    => 'No',
                ],
                'createdAt'            => [
                    'name'        => 'createdAt',
                    'type'        => TYPE_DATETIME,
                    'updatable'   => false,
                    'addable'     => false,
                ],
                'modifiedAt'           => [
                    'name'        => 'modifiedAt',
                    'type'        => TYPE_DATETIME,
                    'nullable'    => 'No',
                ],
                'userId'               => [
                    'name'        => 'userId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'User',
                ],
                'metaInfo'             => [
                    'name'        => 'metaInfo',
                    'type'        => TYPE_ARRAY,
                    'complexType' => 'json',
                    'nullable'    => 'No',
                ],
                'statusId'             => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
                'tagIds'               => [
                    'name'        => 'tagIds',
                    'type'        => TYPE_ARRAY,
                    'complexType' => 'int[]',
                ],
            ],
            'lists'     => [],
            'search'    => [
                'geStartDate'   => [
                    'name'       => 'startDate',
                    'type'       => TYPE_DATE,
                    'searchType' => SEARCHTYPE_GE,
                ],
                'leStartDate'   => [
                    'name'       => 'startDate',
                    'type'       => TYPE_DATE,
                    'searchType' => SEARCHTYPE_LE,
                ],
                '!albumId'      => [
                    'name'       => 'albumId',
                    'type'       => TYPE_INTEGER,
                    'searchType' => SEARCHTYPE_NOT_EQUALS,
                ],
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
                'nnOrderNumber' => [
                    'name'       => 'orderNumber',
                    'type'       => TYPE_BOOLEAN,
                    'searchType' => SEARCHTYPE_NOT_NULL,
                ],
            ],
        ];
    }
