<?php


    /**
     * Photo Factory
     *
     * @package EazyPhoto
     * @subpackage Albums
     *
     * @method static Photo[] Get( $search = null, $options = null, $connectionName = PhotoFactory::DefaultConnection ) Get Objects
     * @method static Photo   GetById( $id, $search = null, $options = null, $connectionName = PhotoFactory::DefaultConnection ) Get Object By Id
     * @method static Photo   GetOne( $search = null, $options = null, $connectionName = PhotoFactory::DefaultConnection ) Get One Object
     * @method static Photo   GetFromRequest( $prefix = null, $connectionName = PhotoFactory::DefaultConnection ) Get Object from Request
     */
    class PhotoFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Photo instance mapping  */
        public static $mapping = [
            'class'     => 'Photo',
            'table'     => 'photos',
            'view'      => 'getPhotos',
            'flags'     => [ 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'photoId'      => [
                    'name'        => 'photoId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'albumId'      => [
                    'name'        => 'albumId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Album',
                ],
                'originalName' => [
                    'name'        => 'originalName',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'filename'     => [
                    'name'        => 'filename',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'fileSize'     => [
                    'name'        => 'fileSize',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'No',
                ],
                'fileSizeHd'   => [
                    'name'        => 'fileSizeHd',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'No',
                ],
                'orderNumber'  => [
                    'name'        => 'orderNumber',
                    'type'        => TYPE_INTEGER,
                ],
                'afterText'    => [
                    'name'        => 'afterText',
                    'type'        => TYPE_STRING,
                    'max'         => 65535,
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'title'        => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'searchType'  => SEARCHTYPE_ILIKE,
                ],
                'exif'         => [
                    'name'        => 'exif',
                    'type'        => TYPE_ARRAY,
                    'complexType' => 'json',
                    'max'         => 65535,
                ],
                'createdAt'    => [
                    'name'        => 'createdAt',
                    'type'        => TYPE_DATETIME,
                    'updatable'   => false,
                    'addable'     => false,
                ],
                'photoDate'    => [
                    'name'        => 'photoDate',
                    'type'        => TYPE_DATETIME,
                ],
                'statusId'     => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
            ],
            'lists'     => [],
            'search'    => [
                '_photoId' => [
                    'name'       => 'photoId',
                    'type'       => TYPE_INTEGER,
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
