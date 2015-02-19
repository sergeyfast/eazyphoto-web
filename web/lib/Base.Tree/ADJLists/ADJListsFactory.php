<?php
    use Eaze\Core\Logger;
    use Eaze\Database\ConnectionFactory;
    use Eaze\Database\SqlCommand;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\BaseFactoryPrepare;
    use Eaze\Modules\MemcacheHelper;

    class ADJListsFactory extends TreeFactory {

        /**
         *
         * @param BaseTreeObject $object         Tree object to add.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Connection name to use.
         * @param array          $withSupport
         * @return bool
         * @see TreeFactory::Add()
         */
        public static function Add( $object, array $mapping, $connectionName = '', $withSupport = [ 'TREEMODE_ADJ' ] ) {
            if ( empty( $object->parent ) ) {
                $object->parentId = null;
            }

            $connection = ConnectionFactory::Get( $connectionName );

            $connection->begin();

            $result = true;

            if ( empty( $object->objectId ) ) {
                $result           = BaseFactory::Add( $object, $mapping, $connectionName );
                $object->objectId = BaseFactory::GetCurrentId( $mapping, $connectionName );
            }

            if ( $result ) {
                $command = ADJListsPrepare::PrepareAddCommand( $mapping['table'] . 'Tree', $connection );
                $cmd     = new SqlCommand( $command, $connection );

                $cmd->SetInt( '@objectId', $object->objectId );
                $cmd->SetInt( '@parentId', $object->parentId );
                $cmd->SetInt( '@level', $object->parent->level + 1 );

                if ( $cmd->ExecuteNonQuery() ) {
                    $connection->commit();
                    return true;
                }
            }

            $connection->rollback();
            return false;
        }


        /**
         *
         * @param array  $mapping        Object mapping.
         * @param string $connectionName Name of the database connection to use.
         * @see TreeFactory::Check()
         */
        public static function Check( $mapping, $connectionName = "" ) {
            //TODO - Insert your code here
        }


        /**
         *
         * @param BaseTreeObject $object         Tree node to copy.
         * @param BaseTreeObject $destination    Destination tree node to copy.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Name of the database connection to use.
         * @see TreeFactory::Copy()
         */
        public static function Copy( $object, $destination, $mapping, $connectionName = null ) {
            //TODO - Insert your code here
        }


        /**
         *
         * @param array  $searchArray
         * @param array  $options
         * @param array  $mapping        Mapping of the object.
         * @param string $connectionName Name of the database connection to use.
         * @return float
         * @see      TreeFactory::Count()
         */
        public static function Count( $searchArray = [ ], $options = [ ], $mapping, $connectionName = "" ) {
            $connection = ConnectionFactory::Get( $connectionName );

            if ( !empty( $object ) ) {
                if ( $object instanceof BaseTreeObject ) {
                    $object = BaseFactory::GetById( $object->objectId, NULL, $mapping, null, $connectionName );
                } else {
                    $object = BaseFactory::GetById( $object, NULL, $mapping, null, $connectionName );
                }
            }

            $result     = ( !empty( $object ) && !empty( $options[OPTION_WITH_PARENT] ) ) ? [ $object->objectId => $object ] : [ ];
            $levelArray = ( empty( $object ) ? [ ] : [ $object->objectId => $object ] );
            $level      = 1;

            while ( true ) {
                Logger::Debug( 'Getting tree level %s', $level );

                if ( !empty( $levelArray ) ) {
                    $searchArray["_parentId"] = array_keys( $levelArray );
                    unset( $levelArray );
                }

                $command = BaseFactoryPrepare::PrepareGetString( $searchArray, $mapping, $options, $connection );
                $cmd     = new SqlCommand( $command, $connection );

                BaseFactory::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );

                if ( BaseFactory::CanPages( $mapping ) ) {
                    $cmd->SetInteger( "@pageOffset", $searchArray[BaseFactoryPrepare::Page] * $searchArray[BaseFactoryPrepare::PageSize] );
                    $cmd->SetInteger( "@pageSize", $searchArray[BaseFactoryPrepare::PageSize] );
                }

                // memcache
                if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                    $cacheKey    = $mapping["class"] . "_query_" . md5( $cmd->GetQuery() );
                    $cacheResult = MemcacheHelper::Get( $cacheKey );

                    if ( !$cacheResult === false ) {
                        $levelArray = $cacheResult;
                    }
                }


                if ( !isset( $levelArray ) || $level == 1 ) {
                    $ds         = $cmd->execute();
                    $levelArray = self::GetResults( $ds, $options, $mapping, $connectionName );
                }

                // memcached hack
                if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                    MemcacheHelper::Replace( $mapping["class"], $cacheKey, $levelArray );
                }

                if ( !empty( $options[OPTION_LEVEL_MAX] ) && $options[OPTION_LEVEL_MAX] < $level ) {
                    break;
                }

                if ( !empty( $options[OPTION_LEVEL_MIN] ) && $options[OPTION_LEVEL_MIN] > $level ) {
                    $level++;
                    continue;
                }

                if ( empty( $levelArray ) ) {
                    break;
                }
                $level++;

                foreach ( $levelArray as $key => $value ) {
                    $result[$value->objectId] = $value;
                }
            }

            // calculate pagecount
            if ( !BaseFactory::CanPages( $mapping, $options ) ) {
                $searchArray[BaseFactoryPrepare::PageSize] = 1;
            }

            if ( 0 == $searchArray[BaseFactoryPrepare::PageSize] ) {
                $searchArray[BaseFactoryPrepare::PageSize] = BaseFactoryPrepare::PageSizeCount;
            }

            return ( count( $result ) / $searchArray[BaseFactoryPrepare::PageSize] );
        }


        /**
         *
         * @param BaseTreeObject $object         Tree node to delete.
         * @param array          $mapping        Mapping of the object
         * @param string         $connectionName Name of the database connection.
         * @param bool           $withObjects    Determines whether deletes objects form the data table.
         * @return bool
         * @see TreeFactory::Delete()
         */
        public static function Delete( $object, $mapping, $connectionName = "", $withObjects = true ) {
            if ( empty( $object ) ) {
                return false;
            }

            if ( empty( $object->objectId ) ) {
                return false;
            }

            $connection = ConnectionFactory::Get( $connectionName );
            $command    = ADJListsPrepare::PrepareDeleteCommand( $mapping, $connection );

            $childrenIds = [ ];
            $ids         = [ $object->objectId ];
            $result      = false;

            $connection->begin();


            while ( !empty( $ids ) ) {
                $childrenIds = array_merge( $childrenIds, $ids );
                $cmd         = new SqlCommand( $command, $connection );

                $cmd->SetList( "@_objectIds", $ids, TYPE_INTEGER );
                //$cmd->SetInteger( "@level", $level ); // WTF???
                $result = $cmd->ExecuteNonQuery();

                if ( !$result ) {
                    $connection->rollback();
                    return false;
                }

                $objects = self::Get( [ "_parentId" => $ids ], [ ], null, $mapping, $connectionName );
                $ids     = array_keys( $objects );
                //$level   = $level + 1; //WTF???
            }

            if ( $withObjects ) {
                $object->statusId = 3;
                $result           = BaseFactory::UpdateByMask( $object, [ "statusId" ], [ "_id" => $childrenIds ], $mapping, $connectionName );
            }

            if ( !$result ) {
                $connection->rollback();
                return false;
            }

            $connection->commit();
            return true;
        }


        /**
         *
         * @param array                         $searchArray
         * @param array                         $options
         * @param        BaseTreeObject|integer $object         Root tree object.
         * @param array                         $mapping        Mapping of the object.
         * @param string                        $connectionName Name of the database connection to use
         * @static
         * @return array
         * @see TreeFactory::Get()
         */
        public static function Get( $searchArray = [ ], $options = [ ], $object = null, $mapping, $connectionName = "" ) {
            $connection = ConnectionFactory::Get( $connectionName );

            if ( !empty( $object ) ) {
                if ( $object instanceof BaseTreeObject ) {
                    $object = BaseFactory::GetById( $object->objectId, NULL, $mapping, null, $connectionName );
                } else {
                    $object = BaseFactory::GetById( $object, NULL, $mapping, null, $connectionName );
                }
            }

            $result     = ( !empty( $object ) && !empty( $options[OPTION_WITH_PARENT] ) ) ? [ $object->objectId => $object ] : [ ];
            $levelArray = ( empty( $object ) ? [ ] : [ $object->objectId => $object ] );
            $level      = 1;

            while ( true ) {
                Logger::Debug( 'Getting tree level %s', $level );

                if ( !empty( $levelArray ) ) {
                    $searchArray["_parentId"] = array_keys( $levelArray );
                    unset( $levelArray );
                }

                $command = BaseFactoryPrepare::PrepareGetString( $searchArray, $mapping, $options, $connection );
                $cmd     = new SqlCommand( $command, $connection );

                BaseFactory::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );

                if ( BaseFactory::CanPages( $mapping ) ) {
                    $cmd->SetInteger( "@pageOffset", $searchArray[BaseFactoryPrepare::Page] * $searchArray[BaseFactoryPrepare::PageSize] );
                    $cmd->SetInteger( "@pageSize", $searchArray[BaseFactoryPrepare::PageSize] );
                }

                // memcache
                if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                    $cacheKey    = $mapping["class"] . "_query_" . md5( $cmd->GetQuery() );
                    $cacheResult = MemcacheHelper::Get( $cacheKey );

                    if ( !$cacheResult === false ) {
                        $levelArray = $cacheResult;
                    }
                }


                if ( !isset( $levelArray ) || $level == 1 ) {
                    $ds         = $cmd->execute();
                    $levelArray = self::GetResults( $ds, $options, $mapping, $connectionName );
                }

                // memcached hack
                if ( !empty( $mapping["flags"]["CanCache"] ) && MemcacheHelper::IsActive() ) {
                    MemcacheHelper::Replace( $mapping["class"], $cacheKey, $levelArray );
                }

                if ( !empty( $options[OPTION_LEVEL_MAX] ) && $options[OPTION_LEVEL_MAX] < $level ) {
                    break;
                }

                if ( !empty( $options[OPTION_LEVEL_MIN] ) && $options[OPTION_LEVEL_MIN] > $level ) {
                    $level++;
                    continue;
                }

                if ( empty( $levelArray ) ) {
                    break;
                }
                $level++;

                foreach ( $levelArray as $key => $value ) {
                    $result[$value->objectId] = $value;
                }
            }

            return $result;
        }


        /**
         *
         * @param BaseTreeObject $object         Start node to get branch.
         * @param array          $mapping        Object mappping to use.
         * @param string         $connectionName Name of the conneciton to use.
         * @return array
         * @see TreeFactory::GetBranch()
         */
        public static function GetBranch( $object, $mapping, $connectionName = null ) {
            if ( $object->level == 1 ) {
                return [ $object ];
            }

            $result = [ $object->objectId => $object ];
            $o      = $object;

            while ( !empty( $o->parentId ) ) {
                $o                    = self::GetById( $o->parentId, [ ], [ ], null, $mapping, $connectionName );
                $result[$o->objectId] = $o;
            }

            return array_reverse( $result );
        }


        /**
         *
         * @param integer        $id             Id of the object.
         * @param array          $searchArray    Search array.
         * @param array          $options        Array of the options to use.
         * @param BaseTreeObject $object         Root object to use.
         * @param array          $mapping        Mapping for the object.
         * @param string         $connectionName Name of hte database connection to use.
         * @return BaseTreeObject
         * @see      TreeFactory::GetById()
         */
        public static function GetById( $id, $searchArray, $options, $object, $mapping, $connectionName ) {
            if ( empty( $id ) ) {
                return null;
            }

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
        }


        /**
         *
         * @param BaseTreeObject $object         Parent tree node.
         * @param array          $searchArray    Array of the search parameters.
         * @param array          $options        Array of the options to use.
         * @param integer        $level          Max level to get the children.
         * @param                $mapping
         * @param string         $connectionName Name of the database connection to use.
         * @return array
         * @see      TreeFactory::GetChildren()
         */
        public static function GetChildren( $object, $searchArray = [ ], $options = [ ], $level = 1, $mapping, $connectionName = null ) {
            $options[OPTION_LEVEL_MIN] = 1;
            $options[OPTION_LEVEL_MAX] = $level;
            return self::Get( $searchArray, $options, $object, $mapping, $connectionName );
        }


        /**
         *
         * @param array  $searchArray    Search array.
         * @param array  $options        Array of the options to use.
         * @param array  $mapping        Mapping for the object.
         * @param string $connectionName Name of hte database connection to use.
         *
         * @return BaseTreeObject
         * @see TreeFactory::GetOne()
         */
        public static function GetOne( $searchArray = [ ], $options = [ ], $mapping = [ ], $connectionName = null ) {
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
         * Moves specified nodes to destination node.
         * @param BaseTreeObject $object         Tree node to move.
         * @param BaseTreeObject $destination    Destination tree node to move.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Name of the database connection to use.
         * @return bool
         * @see TreeFactory::Move()
         */
        public static function Move( $object, $destination, $mapping, $connectionName = null ) {
            $connection = ConnectionFactory::Get( $connectionName );

            $connection->begin();
            $command = ADJListsPrepare::PrepareUpdateCommand( $mapping, $connection );
            $cmd     = new SqlCommand( $command, $connection );

            $cmd->setInt( "@level", $destination->level + 1 );
            $cmd->SetInt( "@objectId", $object->objectId );
            $cmd->SetInt( "@parentId", $destination->objectId );

            $result = $cmd->ExecuteNonQuery();

            if ( !$result ) {
                $connection->rollback();
                return false;
            }

            $command = ADJListsPrepare::PrepareMoveCommand( $mapping, $connection );

            $ids   = [ $object->objectId ];
            $level = $destination->level + 1;

            while ( !empty( $ids ) ) {
                $cmd = new SqlCommand( $command, $connection );

                $cmd->SetList( "@_objectIds", $ids, TYPE_INTEGER );
                $cmd->SetInteger( "@level", $level );
                $result = $cmd->ExecuteNonQuery();

                if ( !$result ) {
                    $connection->rollback();
                    return false;
                }

                $objects = self::Get( [ "_parentId" => $ids ], [ ], null, $mapping, $connectionName );
                $ids     = array_keys( $objects );
                $level   = $level + 1;
            }

            $connection->commit();
            return true;
        }


        /**
         *
         * @param array  $mapping
         * @param string $connectionName Name of the database connection to use.
         * @see TreeFactory::Restore()
         */
        public static function Restore( $mapping, $connectionName = "" ) {

            //TODO - Insert your code here
        }


        /**
         *
         * @param mixed  $object         node to update.
         * @param mixed  $destination    Parent node for the target instance.
         * @param array  $mapping        Object mapping.
         * @param string $connectionName Name of the database connection to use.
         * @return bool
         * @see TreeFactory::Update()
         */
        public static function Update( $object, $destination, $mapping, $connectionName = null ) {
            $connection = ConnectionFactory::Get( $connectionName );

            $connection->begin();

            if ( empty ( $object ) ) {
                return false;
            }
            $vars = get_class_vars( get_class( $object ) . "Factory" );

            $result = BaseFactory::Update( $object, $vars["mapping"], $connectionName );

            if ( is_string( $destination ) ) {
                $ids      = explode( '.', $destination );
                $objectId = $ids[count( $ids ) - 1];

                $destinationNode = BaseTreeFactory::GetById( $objectId, [ ], [ ], null, $mapping, $connectionName );
            } else {
                if ( is_int( $destination ) ) {
                    $destinationNode = BaseTreeFactory::GetById( $destination, [ ], [ ], null, $mapping, $connectionName );
                } else {
                    if ( is_object( $destination ) ) {
                        $destinationNode = $destination;
                    }
                }
            }

            $withMove = !empty( $destinationNode ) && ( $object->parentId !== $destinationNode->objectId );

            if ( $withMove && $result ) {
                $result = self::Move( $object, $destinationNode, $mapping, $connectionName );
            }

            if ( !$result ) {
                $connection->rollback();
                return false;
            }

            $connection->commit();
            return true;
        }
    }