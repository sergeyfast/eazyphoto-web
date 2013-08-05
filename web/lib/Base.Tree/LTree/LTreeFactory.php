<?php    
    /**
     * Tree Factory Class Implements LTREE Storage Mode.
     * 
     * @package Base.Tree
     * @subpackage Base.Tree.LTree
     * @author Rykin Maxim
     * @static 
     * @version $Revision: 1.8 $
     */
    class LTreeFactory extends TreeFactory {
        /**
         * Selects all children of the specified tree node.
         *
         * @param BaseTreeObject $object  Root tree object.
         * @param array $mapping          Mapping of the object.
         * @param string $connectionName  Name of the database connection to use
         * @static 
         * @return array
         */
        public static function Get( $searchArray = array(), $options = array(), $object = null, $mapping, $connectionName = "" ) {
            $connection = ConnectionFactory::Get( $connectionName );
            
            $command = LTreePrepare::PrepareGetCommand( $searchArray, $options, $object, $mapping, $connection );
            $cmd     = new SqlCommand( $command, $connection );
            
            BaseFactory::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );
            
            if ( BaseFactory::CanPages( $mapping ) ) {
                $cmd->SetInteger( "@pageOffset", $searchArray[BaseFactoryPrepare::Page] * $searchArray[BaseFactoryPrepare::PageSize] );
                $cmd->SetInteger( "@pageSize",   $searchArray[BaseFactoryPrepare::PageSize] );
            }
            
            if ( !( empty( $object ) ) ) {
                $cmd->SetParameter( "@path", $object->path );
            }
            
            // memcache
            if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                $cacheKey    = $mapping["class"] . "_query_" . md5($cmd->GetQuery());
                $cacheResult = MemcacheHelper::Get( $cacheKey );
                
                if ( !$cacheResult === false ) {
                    $result = $cacheResult;
                }
            }    
            
            
            if ( !isset( $result ) ) {
                $ds     = $cmd->execute();
                $result = self::GetResults( $ds, $options, $mapping, $connectionName );
            }
            
            // memcached hack
            if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                MemcacheHelper::Replace( $mapping["class"], $cacheKey, $result );
            }
            
            return $result;
        }
        
        /**
         * Get node element by id.
         *
         * @param integer $id             Id of the object.
         * @param array $searchArray      Search array.
         * @param array $options          Array of the options to use.
         * @param BaseTreeObject $object  Root object to use.
         * @param array $mapping          Mapping for the object.
         * @param string $connectionName  Name of hte database connection to use.
         * @param string $mode            Mode of the tree storage.
         * @return BaseTreeObject
         */
        public static function GetById( $id, $searchArray, $options, $object, $mapping, $connectionName ) {
            if (  empty( $id ) ) {
                return null;
            }

            if ( empty( $options[OPTION_WITH_CHILDREN] ) ) {
                $key = null;
                
                foreach ( $mapping["fields"] as $field => $data ) {
                    if ( !empty( $data["key"] ) ) {
                        $key = $field;
                        break;
                    }
                }
                
                if ( empty( $key ) ) {
                    Logger::Warning( 'Class %s has no primary key', $mapping['class'] );
                    return null;    
                }
                
                $searchArray[$key] = $id;
    
                return self::GetOne( $searchArray, $options, $mapping, $connectionName );
            } else {
                $connection = ConnectionFactory::Get( $connectionName );
                $command = LTreePrepare::PrepareGetByIdCommand( $searchArray, $options, $mapping, $connection );
                
                $cmd = new SqlCommand( $command, $connection );
                $mapping["fields"] = array_merge( $mapping["fields"], BaseTreeFactory::$mapping["fields"] );
                
                BaseFactory::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );
                
                if ( empty( $id ) ) {
                    $cmd->SetString( "@path", "*" );
                } else {
                    $cmd->SetString( "@path", "*.$id.*" );
                }
                
                if ( BaseFactory::CanPages( $mapping ) ) {
                    $cmd->SetInteger( "@pageOffset", $searchArray[BaseFactoryPrepare::Page] * $searchArray[BaseFactoryPrepare::PageSize] );
                    $cmd->SetInteger( "@pageSize",   $searchArray[BaseFactoryPrepare::PageSize] );
                }
                
                // memcache
                if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                    $cacheKey    = $mapping["class"] . "_query_" . md5($cmd->GetQuery());
                    $cacheResult = MemcacheHelper::Get( $cacheKey );
                    
                    if ( !$cacheResult === false ) {
                        $result = $cacheResult;
                    }
                }    
                
                
                if ( !isset( $result ) ) {
                    $ds     = $cmd->execute();
                    $result = self::GetResults( $ds, $options, $mapping );
                    $result = BaseTreeHelper::Collapse( $result );
                }
                
                // memcached hack
                if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                    MemcacheHelper::Replace( $mapping["class"], $cacheKey, $result );
                }
                
                
                if ( !empty( $id ) ) {
                    if ( empty( $result[$id] ) ) {
                        return null;
                    } 
    
                    return $result[$id];
                }
                
                return $result;
            }
            
            return null;
        }
        
        
        /**
         * Gets one of the tree elements. 
         *
         * @param array $searchArray      Search array.
         * @param array $options          Array of the options to use.
         * @param array $mapping          Mapping for the object.
         * @param string $connectionName  Name of hte database connection to use.
         * @param string $mode            Mode of the tree storage.
         * @return BaseTreeObject
         */
        public static function GetOne( $searchArray = array(), $options = array(), $mapping = array(), $connectionName = null ) {
            $result = self::Get( $searchArray, $options, null, $mapping, $connectionName );
            
            $result = BaseTreeHelper::Collapse( $result );

            if ( count( $result ) != 1 ) {
                return null;
            }

            if ( is_array( $result ) ) {
                foreach ( $result as $object ) {
                    return $object;
                }
            }

            return $result;
        }
        
        
        /**
         * Selects count of the element.
         *
         * @param BaseTreeObject $object  Root tree object.
         * @param array $mapping          Mapping of the object.  
         * @param string $connectionName  Name of the database connection to use.
         */
        public static function Count( $searchArray = array(), $options = array(), $mapping, $connectionName = "" ) {
            $mapping["fields"] = array_merge( $mapping["fields"], BaseTreeFactory::$mapping["fields"] );
            
            $conn     = ConnectionFactory::Get( $connectionName );
            $cmd      = new SqlCommand( LTreePrepare::PrepareCountCommand( $searchArray, $options, null, $mapping, $conn ), $conn );
            $cacheKey = "";

            BaseFactory::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );

            // memcache
            if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                $cacheKey    = $mapping["class"] . "_query_" . md5($cmd->GetQuery());
                $cacheResult = MemcacheHelper::Get( $cacheKey );

                if ( $cacheResult !== false ) {
                    $count = $cacheResult;
                }
            }


            if ( !isset( $count ) ) {
                $ds = $cmd->execute();
                if ( true == $ds->next() ) {
                    $count = $ds->getInteger( "count" );
                } else {
                    $count = 0;
                }

                if ( is_null( $count ) ) {
                    $count = 0;
                }
            }

            // memcached hack
            if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                $expires = empty( $mapping["cache"] ) ? 3600 : $mapping["cache"];
                MemcacheHelper::Replace( $mapping["class"], $cacheKey, $count, 0,  $expires );
            }
            // OEF memcache

            // calculate pagecount
            if ( !BaseFactory::CanPages( $mapping, $options ) ) {
                $searchArray[BaseFactoryPrepare::PageSize] = 1;
            }

            if ( 0 == $searchArray[BaseFactoryPrepare::PageSize] ) {
                $searchArray[BaseFactoryPrepare::PageSize] = BaseFactoryPrepare::PageSizeCount;
            }

            return ( $count / $searchArray[BaseFactoryPrepare::PageSize] );
        }
        

        /**
         * Adds new object to the tree.
         *
         * @param BaseTreeObject $object  Tree object to add.
         * @param array $mapping          Mapping of the object.
         * @param string $connectionName  Connection name to use.
         * @param array $withSupport      Defines storage mode to support.
         */
        public static function Add( $object, array $mapping, $connectionName = "", $withSupport = array( "TREEMODE_ADJ" ) ) {
            if ( empty( $object->parent ) ) {
                $object->parentId = null;
            }

            $connection = ConnectionFactory::Get( $connectionName ) ;
            
            $connection->begin();
            
            $result = true;
            
            if ( empty( $object->objectId ) ) {
                $result           = BaseFactory::Add( $object, $mapping, $connectionName );
                $object->objectId = BaseFactory::GetCurrentId( $mapping, $connectionName );
            }
            
            if ( $result ) {
                $object->path     = ltrim( $object->GetParentPath() . "." . $object->objectId, '.' );
                
                $command = LTreePrepare::PrepareAddCommand( $mapping["table"] . "Tree", $connection );
                $cmd = new SqlCommand( $command, $connection );
                
                $cmd->SetInt( "@objectId", $object->objectId );
                $cmd->SetInt( "@parentId", $object->parentId );
                $cmd->SetString( "@path",  $object->path );

                if ( $cmd->ExecuteNonQuery() ) {
                    $connection->commit();
                    return true;
                }
            }
            
            $connection->rollback();
            return false;
        }
        

        /**
         * Deletes specified tree node.
         *
         * @param BaseTreeObject $object  Tree node to delete.
         * @param array $mapping          Mapping of the object
         * @param string $connectionName  Name of the database connection.
         * @param bool $withObjects       Determines whether deletes objects form the data table.
         */
        public static function Delete( $object, $mapping, $connectionName = "", $withObjects = true ) {
            if ( empty( $object ) ) {
                return false;
            }
            
            if ( empty( $object->objectId ) ) {
                return false;
            }
            
            $connection = ConnectionFactory::Get( $connectionName );
            
            $command = LTreePrepare::PrepareDeleteStatement( $mapping, $connection );
            $cmd = new SqlCommand(
                $command
                , $connection
            );
            
            $cmd->SetString( "@path",  $object->path . ( ( $connection instanceof PgSqlConnection  ) ? ".*" : "%" ) );

            $children = self::Get( array(), array( OPTION_WITH_PARENT => true ), $object, $mapping, $connectionName );
            
            $ids = array();
            
            foreach ( $children as $child ) {
                $ids[]           = $child->objectId;
            }
            
            $connection->begin();
            
            $result = $cmd->ExecuteNonQuery();
            
            if ( !$result ) {
                $connection->rollback();
                return false;
            }
            
            if ( $withObjects ) {
                $object->statusId = 3;
                $result = BaseFactory::UpdateByMask( $object, array( "statusId" ), array( "_id" => $ids ), $mapping, $connectionName );
            }
            
            if ( !$result ) {
                $connection->rollback();
                return false;
            }
            
            $connection->commit();
            return true;
        }
        
        
        /**
         * Moves tree node to the other node.
         *
         * @param BaseTreeNode $object       Tree node to move.
         * @param BaseTreeNode $destination  Destination tree node to move.
         * @param array $mapping             Mapping of the object.   
         * @param string $connectionName     Name of the database connection to use.
         */
        public static function Move( $object, $destination, $mapping, $connectionName = null ) {
            $connection = ConnectionFactory::Get( $connectionName );
            $command = "";

            $LTREECommand = LTreePrepare::PrepareMoveStatement( $mapping, $connection );
            
            $connection->begin();

            $LTREECommandResult = true;

            $LTREECmd = new SqlCommand(
                $LTREECommand
                , $connection
            );
            
            $LTREECmd->SetParameter( "@oldParentPathLev", $object->path . ".*" );
            $LTREECmd->SetParameter( "@oldParentPath",    $object->path );
            $LTREECmd->SetParameter( "@newParentPath",    ( empty( $destination->path ) ? $object->objectId : $destination->path . "." . $object->objectId ) );
            
            $result = "UPDATE " . $connection->quote( $mapping["table"] . "Tree" );
            $result .= " SET " .  $connection->quote( "parentId" ) . " = @parentId";
            $result .= " WHERE " . $connection->quote( "objectId" ) . " = @objectId";

            $cmd = new SqlCommand( 
                $result
                , $connection
            );
            
            $cmd->SetInteger( "@parentId", $destination->objectId );
            $cmd->SetInteger( "@objectId", $object->objectId );
            
            $cmdResult          = $cmd->ExecuteNonQuery();
            $LTREECommandResult = $LTREECmd->ExecuteNonQuery();
            
            if ( !( $LTREECommandResult && $cmdResult ) ) {
                $connection->rollback();
                return false;
            }

            $connection->commit();
            return true;
        }
        

        /**
         * Copies tree node to the other node.
         *
         * @param BaseTreeNode $object       Tree node to copy.
         * @param BaseTreeNode $destination  Destination tree node to copy.
         * @param array $mapping             Mapping of the object.   
         * @param string $connectionName     Name of the database connection to use.
         */
        public static function Copy( $object, $destination, $mapping, $connectionName = null ) {
            $connection = ConnectionFactory::Get( $connectionName );
            $connection->begin();
            
            if( !empty( $object->object ) ) {
                $object = self::GetById( $object->objectId,array(), array( OPTION_WITH_CHILDREN => 1 ), null, $mapping, $connectionName );
            }
            
            if ( empty( $destination ) ) {
                $object->parentId = null;
                $object->parent   = null;
                $object->objectId = null;
            } else {
                $object->parentId = $destination->objectId;
                $object->parent   = $destination;
                $object->objectId = null;
            }
            
            $result = self::Add( $object, $mapping, $connectionName, $mode );
            
            if (!$result) {
                $connection->rollback();
                return false;
            }
            
            foreach( $object->nodes as $child ) {
                $result = self::Copy( $child, $object, $mapping, $connectionName, $mode );
                
                if (!$result) {
                    $connection->rollback();
                    return false;
                }
            }
            
            $connection->commit();
            return true;
        }
        
        /**
         * Updates tree node data and/or tree structure
         *
         * @param mixed $object           node to update.
         * @param mixed $destination      Parent node for the target instance.
         * @param array $mapping          Object mapping.
         * @param string $connectionName  Name of the database connection to use.
         * @param mode $mode              Tree mode.
         */
        public static function Update( $object, $destination, $mapping, $connectionName = null ) {
            $connection = ConnectionFactory::Get( $connectionName );
            
            $connection->begin();
            
            if ( empty ( $object ) ) {
                return false;
            }
            
            $vars = get_class_vars( get_class($object) . "Factory" );
            $result = BaseFactory::Update( $object, $vars["mapping"], $connectionName );
            
            if ( is_string( $destination ) ) {
                $ids            = explode( '.', $destination );
                $objectId       = $ids[count( $ids ) - 1];
                
                $destinationNode = BaseTreeFactory::GetById( $objectId, array(), array(), null, $mapping, $connectionName );
            } else if ( is_object( $destination ) ) {
            	$destinationNode = $destination;
            }
            
            $withMove = !empty($destinationNode) && ( $object->GetParentPath() !== $destinationNode->path );
            
            if ( $withMove && $result) {
                $result = self::Move( $object, $destinationNode, $mapping, $connectionName );
            }
            
            if ( !$result ) {
                $connection->rollback();
                return false;
            }
            
            $connection->commit();
            return true;
        }

        
        /**
         * Validates the tree. 
         *
         * @param array $mapping          Object mapping.
         * @param string $connectionName  Name of the database connection to use.
         */
        public static function Check( $mapping, $connectionName = "" ) {
            $treeArray = self::Get( array(), array(), null, $mapping );

            $normal = true;
            
            /// TODO: Add here if the got array is the tree
            
            return $normal;
        }
        
        
        /**
         * Checks if node is in recursive chain.
         *
         * @param BaseTreeObject $node  Current node in the branch.
         * @param Array $keys           Array of childrens' keys
         * @param Array $treeArray      Array of the tree.
         * @return bool
         */
        private static function checkNode( $node, $keys, $treeArray ) {
            if ( $node->parentId == null ) {
                return true;
            }
            
            if ( in_array( $node->parentId, $keys ) ) {
                return false;
            }
            
            $keys[] = $node->objectId;
            
            return self::checkNode( $treeArray[$node->parentId], $keys, $treeArray );
        }
        
        
        /**
         * Restore tree from the base table.
         *
         * @param array $mapping
         * @param string $connectionName Name of the database connection to use.
         */
        public static function Restore( $mapping, $connectionName = null  ) {
            /// TODO: Add here restoring function from base ADJ_LIST tree.

            $connection = ConnectionFactory::Get( $connectionName );
            
            if ( empty( $connection ) ) {
                Logger::Error( 'Connection named %s was not found!', $connectionName );
            }
            
            Logger::VarDump( $connectionName );
            
            $cmd = new SqlCommand ( 'SELECT * FROM ' . $connection->quote( $mapping["table"] ) . ' WHERE ' . $connection->quote( "statusId" ) . " <> 3", $connection );
            
            $ds         = $cmd->Execute();
            $objectTree = BaseFactory::GetObjectTree( $ds->Columns );
            
            foreach ( $mapping["fields"] as $name => $values ) {
                if ( $values["key"] ) {
                    $keyField = $name;
                    break;
                }
            }
            
            $parentId = "parent" . ucfirst( $keyField );
            
            while ( $ds->next() ) {
                $id = $ds->getInteger( "" ); 

                $object = BaseFactory::GetObject( $ds, $mapping, $objectTree );
                
                $object->objectId = $object->$keyField;
                $object->parentId = $object->$parentId;
                
                $objects[$object->$keyField] = $object;
            }
            
            $objects   = BaseTreeHelper::Collapse( $objects );
            $treeTable = $mapping["table"] . "Tree";
            
            $level    = $objects;
            $newLevel = array();
            
            while ( !empty( $level ) ) {
                $newLevel = array();
                
                foreach ( $level as $object ) {
                    $cmd = new SqlCommand(
                        ( ( empty( $object->parentId ) ) 
                            ? LTreePrepare::PrepareInitCommand( $mapping, $connection ) 
                            : LTreePrepare::PrepareRestoreCommand( $mapping, $connection ) 
                        )
                        , $connection 
                    );
                    
                    $cmd->SetInteger( "@objectId", $object->objectId );
                    $cmd->SetInteger( "@parentId", $object->parentId );
                    $cmd->SetInteger( "@level", $object->level );
                    
                    $cmd->Execute();
                    
                    $newLevel = array_merge( $newLevel, $object->nodes );
                }
                
                $level = $newLevel;
            }
        }
        
        
        /**
         * Gets the branch of the specified node.
         *
         * @param BaseTreeNode $object          Object to get branch.
         * @param array        $mapping         Object mapping array.
         * @param string       $connectionName  Connection name to use in query.
         * @return array
         */
        public static function GetBranch( $object, $mapping, $connectionName = null ) {
            if ( $object->level == 0 ) {
                return array( $object );
            }
            
            $connection = ConnectionFactory::Get( $connectionName );
            
            array_merge( $mapping, BaseTreeFactory::$mapping );
            
            $command = LTreePrepare::PrepareBranchStatement( array( '_id' => explode( '.', $object->path ) ), array(), $mapping, $connection );
            
            $cmd = new SqlCommand(
                $command
                , $connection
            );
            
            //            $cmd->SetString( "@path", $object->path );
            BaseFactory::ProcessSearchParameters( array( '_id' => explode( '.', $object->path ) ), $mapping, array(), $cmd );
                        
            $ds = $cmd->Execute();
            
            return self::GetResults( $ds, array(), $mapping );
        }
        
        /**
         * Gets children nodes for specified level.
         *
         * @param BaseTreeNode $object    Parent tree node.
         * @param array $searchArray      Array of the search parameters.
         * @param array $options          Array of the options to use.
         * @param integer $level          Max level to get the children.
         * @param string $connectionName  Name of the database connection to use.
         * @param string $mode            Mode to use.
         * @return array
         */
        public static function GetChildren( $object, $searchArray = array(), $options = array(), $level = 1, $mapping, $connectionName = null, $mode = TREEMODE_LTREE ) {
            $connection = ConnectionFactory::Get( $connectionName );

            if ( empty( $connection ) ) {
                return null;
            }
            
            $command = LTreePrepare::PrepareChildrenStatement( $searchArray, $options, $mapping, $connection );

            $cmd = new SqlCommand(
                $command
                , $connection
            );
            
            BaseFactory::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );
            
            if ( $connection instanceof PgSqlConnection ) {
                $parent = ( ( empty( $options[OPTION_WITH_PARENT] ) ) ? 1 : 0 );
            } else {
                if ( empty( $object->path ) ) {
                    $object = self::GetById( $object->objectId, array(), array(), null, $mapping, $connectionName );
                }
                
                $parent = $object->level - ( ( empty( $options[OPTION_WITH_PARENT] ) ) ? 0 : 1 );
                $level  = $object->level + $level;
            }
            
            $cmd->setInteger( "@objectId", $object->objectId );
            $cmd->SetInteger( "@parent", $parent );
            $cmd->SetInteger( "@level", $level );
            
            $ds = $cmd->Execute();
            
            return self::GetResults( $ds, $options, $mapping );
        }
    }
?>