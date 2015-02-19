<?php


    use Eaze\Database\IConnection;
    use Eaze\Model\BaseFactoryPrepare;

    class ADJListsPrepare {

        /**
         * @param             $searchArray
         * @param             $options
         * @param             $object
         * @param             $mapping
         * @param IConnection $connection
         * @return string
         */
        public static function PrepareGetCommand( $searchArray, $options, $object, $mapping, $connection ) {
            $query = 'SELECT * FROM  ' . $connection->Quote( $mapping["view"] );
            $query .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $connection );
            $query .= BaseFactoryPrepare::GetOrderByString( $options, $connection );

            return $query;
        }


        /**
         * @param             $table
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareAddCommand( $table, IConnection $conn ) {
            $result = "INSERT INTO " . $conn->quote( $table )
                . sprintf( " ( %s ", $conn->quote( "objectId" ) )
                . sprintf( " , %s ", $conn->quote( "parentId" ) )
                . sprintf( " , %s ", $conn->quote( "level" ) )
                . " ) VALUES ( "
                . " @objectId"
                . " , @parentId"
                . " , @level"
                . ");";

            return $result;
        }


        /**
         * @param             $mapping
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareMoveCommand( $mapping, $conn ) {
            $result = "UPDATE " . $conn->quote( $mapping["table"] . "Tree" );
            $result .= " SET level = @level";
            $result .= " WHERE " . $conn->quote( "objectId" ) . " IN @_objectIds";

            return $result;
        }


        /**
         * @param             $mapping
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareUpdateCommand( $mapping, $conn ) {
            $result = "UPDATE " . $conn->quote( $mapping["table"] . "Tree" );
            $result .= " SET level = @level";
            $result .= " , " . $conn->quote( "parentId" ) . " = @parentId";
            $result .= " WHERE " . $conn->quote( "objectId" ) . " = @objectId";

            return $result;
        }


        /**
         * @param             $mapping
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareDeleteCommand( $mapping, $conn ) {
            $result = "DELETE FROM " . $conn->quote( $mapping["table"] . "Tree" );
            $result .= " WHERE " . $conn->quote( "objectId" ) . " IN @_objectIds";

            return $result;
        }
    }