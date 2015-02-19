<?php
    use Eaze\Database\IConnection;
    use Eaze\Database\PgSql\PgSqlConnection;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\BaseFactoryPrepare;

    /**
     * Base Tree Object Factory
     *
     * @package    Base
     * @subpackage Base.Tree
     * @author     Rykin Maxim
     */
    class LTreePrepare {

        public static function PrepareAddCommand( $table, IConnection $conn ) {
            $result = "INSERT INTO " . $conn->quote( $table )
                . sprintf( " ( %s ", $conn->quote( "objectId" ) )
                . sprintf( " , %s ", $conn->quote( "parentId" ) )
                . sprintf( " , %s ", $conn->quote( "path" ) )
                . " ) VALUES ( "
                . " @objectId"
                . " , @parentId"
                . " , @path"
                . ");";

            return $result;
        }


        /**
         * @static
         * @param array        $searchArray
         * @param              $options
         * @param null         $object
         * @param              $mapping
         * @param  IConnection $conn
         * @return string
         */
        public static function PrepareGetCommand( $searchArray = [ ], $options, $object = null, $mapping, $conn ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $options, $conn->GetName() );
            $query       = 'SELECT * FROM  ' . $conn->quote( $mapping["view"] );
            $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
            $query .= self::prepareLQuery( $object, $options, $conn );
            $query .= BaseFactoryPrepare::GetOrderByString( $options, $conn );

            if ( BaseFactory::CanPages( $mapping, $options ) ) {
                $query .= " LIMIT @pageSize OFFSET @pageOffset ";
            }

            return $query;
        }


        /**
         * @static
         * @param array        $searchArray
         * @param              $options
         * @param null         $object
         * @param              $mapping
         * @param  IConnection $conn
         * @return string
         */
        public static function PrepareCountCommand( $searchArray = [ ], $options, $object = null, $mapping, $conn ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $options, $conn->GetName() );
            $query       = 'SELECT COUNT(*) FROM  ' . $conn->quote( $mapping["view"] );
            $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
            $query .= self::prepareLQuery( $object, $options, $conn );
            $query .= BaseFactoryPrepare::GetOrderByString( $options, $conn );

            return $query;
        }


        /**
         * @static
         * @param array        $searchArray
         * @param              $options
         * @param              $mapping
         * @param  IConnection $conn
         * @return string
         */
        public static function PrepareGetByIdCommand( $searchArray = [ ], $options, $mapping, $conn ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $options, $conn->GetName() );
            $query       = 'SELECT * FROM  ' . $conn->quote( $mapping["view"] );
            $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
            $query .= self::prepareLikeLQuery( $conn );
            $query .= BaseFactoryPrepare::GetOrderByString( $options, $conn );

            if ( BaseFactory::CanPages( $mapping, $options ) ) {
                $query .= " LIMIT @pageSize OFFSET @pageOffset ";
            }

            return $query;
        }


        public static function PrepareDeleteStatement( $mapping, $connection ) {
            if ( $connection instanceof PgSqlConnection ) {
                $query = sprintf(
                    "DELETE FROM %s"
                    . " WHERE %s ~ %s"
                    , $connection->quote( $mapping["table"] . "Tree" )
                    , $connection->quote( "path" )
                    , "lquery( @path )"
                );
            } else {
                $query = sprintf(
                    "DELETE FROM %s"
                    . " WHERE %s LIKE %s"
                    , $connection->quote( $mapping["table"] . "Tree" )
                    , $connection->quote( "path" )
                    , "@path"
                );
            }

            return $query;
        }


        public static function PrepareCheckCommand( $mapping, $conn ) {
        }


        public static function PrepareMoveStatement( $mapping, $conn ) {
            if ( $conn instanceof PgSqlConnection ) {
                $result = "UPDATE " . $conn->quote( $mapping["table"] . "Tree" );
                $result .= " SET " . $conn->quote( "path" ) . " = text2ltree( replace( ltree2text( " . $conn->quote( "path" ) . " ), @oldParentPath, @newParentPath ) )";
                $result .= " WHERE " . $conn->quote( "path" ) . " ~ lquery( @oldParentPathLev )";
            } else {
                $result = "UPDATE " . $conn->quote( $mapping["table"] . "Tree" );
                $result .= " SET " . $conn->quote( "path" ) . " = replace( " . $conn->quote( "path" ) . ", @oldParentPath, @newParentPath )";
                $result .= " WHERE " . $conn->quote( "path" ) . " LIKE @oldParentPathLev";
            }
            return $result;
        }


        public static function prepareLQuery( $object, $options, $conn ) {
            $i = ( empty( $options[OPTION_WITH_PARENT] ) ) ? 1 : 0;
            $i = ( empty( $options[OPTION_LEVEL_MIN] ) ) ? $i : $options[OPTION_LEVEL_MIN];

            if ( $conn instanceof PgSqlConnection ) {
                $result = sprintf( " AND %s ~ lquery('%s*{%s,%s}') "
                    , $conn->quote( "path" )
                    , ( ( empty( $object ) ) ? "" : $object->path . '.' )
                    , $i
                    , ( ( empty( $options[OPTION_LEVEL_MAX] ) ) ? '' : $options[OPTION_LEVEL_MAX] )
                );
            } else {
                $result = sprintf( " AND ( %s LIKE '%s' %s ) %s %s"
                    , $conn->quote( "path" )
                    , ( ( empty( $object ) ) ? "%" : $object->path . '.%' )
                    , ( ( empty( $options[OPTION_WITH_PARENT] ) ) ? "" : " OR " . $conn->quote( "path" ) . " LIKE '" . ( ( empty( $object ) ) ? "%" : $object->path ) . "'" )
                    , ( " AND " . $conn->quote( "level" ) . " >= " . ( ( ( empty( $object ) ) ? 0 : $object->level ) + $i - 1 ) )
                    , ( ( empty( $options[OPTION_LEVEL_MAX] ) ) ? '' : " AND " . $conn->quote( "level" ) . " < " . ( ( empty( $object ) ) ? 0 : $object->level ) + $options[OPTION_LEVEL_MAX] - 1 )
                );
            }

            return $result;
        }


        private static function prepareLikeLQuery( $connection ) {
            $result = sprintf( "AND %s ~ lquery( @path )", $connection->quote( "path" ) );
            return $result;
        }


        /**
         * @static
         * @param              $searchArray
         * @param              $options
         * @param              $mapping
         * @param  IConnection $conn
         * @return string
         */
        public static function PrepareChildrenStatement( $searchArray, $options, $mapping, $conn ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $mapping, $conn->GetName() );

            if ( $conn instanceof PgSqlConnection ) {
                $query = 'SELECT * FROM  ' . $conn->quote( $mapping["view"] );
                $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
                $query .= ' AND ' . $conn->quote( "path" ) . " ~ lquery( '*.@objectId.*{@parent,@level}' )";
            } else {
                $query = 'SELECT * FROM  ' . $conn->quote( $mapping["view"] );
                $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
                $query .= ' AND 1 = (' . $conn->quote( "path" ) . " REGEXP '(^|.*\.)@objectId($|\..*)' )";
                $query .= ' AND ' . $conn->quote( "level" ) . " >= @parent";
                $query .= ' AND ' . $conn->quote( "level" ) . " <= @level ";
            }

            return $query;
        }


        /**
         * @static
         * @param              $searchArray
         * @param              $options
         * @param              $mapping
         * @param  IConnection $conn
         * @return string
         */
        public static function PrepareBranchStatement( $searchArray, $options, $mapping, $conn ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $options, $conn->GetName() );

            $query = 'SELECT * FROM  ' . $conn->quote( $mapping["view"] );
            $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );

            return $query;
        }


        /**
         * Prepares restore command from base table.
         *
         * @param array       $mapping    Object mapping.
         * @param IConnection $connection Database connection to prepare.
         * @return string
         */
        public static function PrepareRestoreCommand( $mapping, $connection ) {
            $tree = $mapping["table"] . "Tree";

            if ( $connection instanceof PgSqlConnection ) {
                $query = sprintf( 'INSERT INTO %s ( "objectId", "parentId", "path", "level" )'
                    . " SELECT @objectId, @parentId, text2ltree( COALESCE( ltree2text( %s ) || '.', '' ) || '@objectId' ) , %s + 1"
                    . " FROM %s"
                    . " WHERE %s = @parentId"
                    , $connection->quote( $tree )
                    , $connection->quote( "path" )
                    , $connection->quote( "level" )
                    , $connection->quote( $tree )
                    , $connection->quote( "objectId" )
                );
            } else {
                $query = sprintf( 'INSERT INTO %s ( "objectId", "parentId", "path", "level" )'
                    . " SELECT @objectId, @parentId, COALESCE( %s || '.', '' ) || '@objectId', %s + 1"
                    . " FROM %s"
                    . " WHERE %s = @parentId"
                    , $connection->quote( $tree )
                    , $connection->quote( "path" )
                    , $connection->quote( "level" )
                    , $connection->quote( $tree )
                    , $connection->quote( "objectId" )
                );
            }

            return $query;
        }


        public static function PrepareInitCommand( $mapping, $connection ) {
            $tree = $mapping["table"] . "Tree";

            if ( $connection instanceof PgSqlConnection ) {
                $query = sprintf( 'INSERT INTO %s ( "objectId", "parentId", "path", "level" )'
                    . " SELECT @objectId, @parentId, text2ltree( '@objectId' ), 0"
                    , $connection->quote( $tree )
                    , $connection->quote( "path" )
                    , $connection->quote( "level" )
                );
            } else {
                $query = sprintf( 'INSERT INTO %s ( "objectId", "parentId", "path", "level" )'
                    . " SELECT @objectId, @parentId, '@objectId', 0"
                    , $connection->quote( $tree )
                    , $connection->quote( "path" )
                    , $connection->quote( "level" )
                );
            }

            return $query;
        }
    }