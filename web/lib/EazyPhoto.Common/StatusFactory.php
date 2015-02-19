<?php


    /**
     * Status Factory
     *
     * @package EazyPhoto
     * @subpackage Common
     *
     * @method static Status[] Get( $search = null, $options = null, $connectionName = StatusFactory::DefaultConnection ) Get Objects
     * @method static Status   GetById( $id, $search = null, $options = null, $connectionName = StatusFactory::DefaultConnection ) Get Object By Id
     * @method static Status   GetOne( $search = null, $options = null, $connectionName = StatusFactory::DefaultConnection ) Get One Object
     * @method static Status   GetFromRequest( $prefix = null, $connectionName = StatusFactory::DefaultConnection ) Get Object from Request
     */
    class StatusFactory implements Eaze\Model\IFactory {

        use \Eaze\Model\TBaseFactory;

        /** Default Connection Name */
        const DefaultConnection = null;

        /** Status instance mapping  */
        public static $mapping = [
            'class'     => 'Status',
            'table'     => 'statuses',
            'view'      => 'getStatuses',
            'flags'     => [ 'WithoutTemplates' => 'WithoutTemplates' ],
            'cacheDeps' => ['TODO'],
            'cache'     => 'TODO',
            'fields'    => [
                'statusId' => [
                    'name'        => 'statusId',
                    'type'        => TYPE_INTEGER,
                    'key'         => true,
                ],
                'title'    => [
                    'name'        => 'title',
                    'type'        => TYPE_STRING,
                    'max'         => 255,
                    'nullable'    => 'CheckEmpty',
                ],
                'alias'    => [
                    'name'        => 'alias',
                    'type'        => TYPE_STRING,
                    'max'         => 64,
                    'nullable'    => 'CheckEmpty',
                ],
            ],
            'lists'     => [],
            'search'    => [],
        ];
    }
