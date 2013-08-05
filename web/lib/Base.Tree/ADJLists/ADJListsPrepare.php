<?php
    class ADJListsPrepare {
        public static function PrepareGetCommand( $searchArray, $options, $object, $mapping, $connection ) {
            $query       = 'SELECT * FROM  ' . $conn->quote( $mapping["view"] );
            $query      .= BaseFactoryPrepare::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
            $query      .= BaseFactoryPrepare::GetOrderByString( $options, $conn );
            
            return $query;
        }

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

        public static function PrepareMoveCommand( $mapping, $conn ) {
            $result = "UPDATE " . $conn->quote( $mapping["table"] . "Tree" );
            $result .= " SET level = @level";
            $result .= " WHERE " . $conn->quote( "objectId" ) . " IN @_objectIds";

            return $result;
        }

        public static function PrepareUpdateCommand( $mapping, $conn ) {
            $result = "UPDATE " . $conn->quote( $mapping["table"] . "Tree" );
            $result .= " SET level = @level";
            $result .= " , " . $conn->quote( "parentId" ) . " = @parentId";
            $result .= " WHERE " . $conn->quote( "objectId" ) . " = @objectId";

            return $result;
        }

        public static function PrepareDeleteCommand( $mapping, $conn ) {
            $result = "DELETE FROM " . $conn->quote( $mapping["table"] . "Tree" );
            $result .= " WHERE " . $conn->quote( "objectId" ) . " IN @_objectIds";

            return $result;
        }
    }
?>