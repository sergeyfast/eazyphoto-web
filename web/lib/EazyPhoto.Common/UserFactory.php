<?php


    /**
     * User Factory
     *
     * @package %project%
     * @subpackage Common
     *
     * @method static User[] Get( $search = null, $options = null, $connectionName = UserFactory::DefaultConnection ) Get Objects
     * @method static User   GetById( $id, $search = null, $options = null, $connectionName = UserFactory::DefaultConnection ) Get Object By Id
     * @method static User   GetOne( $search = null, $options = null, $connectionName = UserFactory::DefaultConnection ) Get One Object
     * @method static User   GetFromRequest( $prefix = null, $connectionName = UserFactory::DefaultConnection ) Get Object from Request
     */
    class UserFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** User instance mapping  */
        public static $mapping = [
            'class'     => 'User',
            'table'     => 'users',
            'view'      => 'getUsers',
            'flags'     => [ 'CanPages' => 'CanPages', 'CanCache' => 'CanCache' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'userId'         => [
                    'name'        => 'userId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'login'          => [
                    'name'        => 'login',
                    'type'        => TYPE_STRING,
                    'max'         => 64,
                    'nullable'    => 'CheckEmpty',
                ],
                'password'       => [
                    'name'        => 'password',
                    'type'        => TYPE_STRING,
                    'max'         => 64,
                    'nullable'    => 'CheckEmpty',
                ],
                'lastActivityAt' => [
                    'name'        => 'lastActivityAt',
                    'type'        => TYPE_DATETIME,
                ],
                'authKey'        => [
                    'name'        => 'authKey',
                    'type'        => TYPE_STRING,
                    'max'         => 32,
                ],
                'statusId'       => [
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
                '!userId'  => [
                    'name'       => 'userId',
                    'type'       => TYPE_INTEGER,
                    'searchType' => SEARCHTYPE_NOT_EQUALS,
                ],
            ],
        ];
    }
