<?php


    /**
     * VfsFile Factory
     *
     * @package Base
     * @subpackage VFS
     *
     * @method static VfsFile[] Get( $search = null, $options = null, $connectionName = VfsFileFactory::DefaultConnection ) Get Objects
     * @method static VfsFile   GetById( $id, $search = null, $options = null, $connectionName = VfsFileFactory::DefaultConnection ) Get Object By Id
     * @method static VfsFile   GetOne( $search = null, $options = null, $connectionName = VfsFileFactory::DefaultConnection ) Get One Object
     * @method static VfsFile   GetFromRequest( $prefix = null, $connectionName = VfsFileFactory::DefaultConnection ) Get Object from Request
     */
    class VfsFileFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** VfsFile instance mapping  */
        public static $mapping = [
            'class'     => 'VfsFile',
            'table'     => 'vfsFiles',
            'view'      => 'getVfsFiles',
            'flags'     => [ 'CanPages' => 'CanPages', 'CanCache' => 'CanCache', 'WithoutTemplates' => 'WithoutTemplates' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'fileId'     => [
                    'name'        => 'fileId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'folderId'   => [
                    'name'        => 'folderId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'VfsFolder',
                ],
                'title'      => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'path'       => [
                    'name'        => 'path',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'params'     => [
                    'name'        => 'params',
                    'type'        => TYPE_ARRAY,
                    'complexType' => 'json',
                ],
                'isFavorite' => [
                    'name'        => 'isFavorite',
                    'type'        => TYPE_BOOLEAN,
                ],
                'mimeType'   => [
                    'name'        => 'mimeType',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'fileSize'   => [
                    'name'        => 'fileSize',
                    'type'        => TYPE_INTEGER,
                ],
                'fileExists' => [
                    'name'        => 'fileExists',
                    'type'        => TYPE_BOOLEAN,
                    'nullable'    => 'No',
                ],
                'statusId'   => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'nullable'    => 'CheckEmpty',
                    'foreignKey'  => 'Status',
                ],
                'createdAt'  => [
                    'name'        => 'createdAt',
                    'type'        => TYPE_DATETIME,
                    'updatable'   => false,
                    'addable'     => false,
                ],
            ],
            'lists'     => [],
            'search'    => [
                'title%'   => [
                    'name'       => 'title',
                    'type'       => TYPE_STRING,
                    'searchType'   => SEARCHTYPE_RIGHT_LIKE,
                ],
                '_fileId'  => [
                    'name'       => 'fileId',
                    'type'       => TYPE_INTEGER,
                    'searchType'   => SEARCHTYPE_ARRAY,
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
