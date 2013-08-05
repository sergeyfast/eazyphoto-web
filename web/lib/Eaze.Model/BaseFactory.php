<?php
    /**
     * Base Factory
     *
     * @package Eaze
     * @subpackage Model
     * @author sergeyfast
     */
    class BaseFactory {

        /**
         * Select Many-to-many Lists
         * @since 1.2 (before: OPTION_WITH_LISTS)
         */
        const WithLists = 'withLists';

        /**
         * Hide Disable
         * @since 1.2 (before: OPTION_HIDE_DISABLED)
         */
        const WithoutDisabled = 'hideDisabled';

        /**
         * Without Cache
         * @since 1.2 (before: OPTION_NO_CACHE)
         */
        const WithoutCache = 'noCache';

        /**
         * Set Cache Time
         * @since 1.2 (before: OPTION_CACHE_TIME)
         */
        const CacheTime = 'cacheTime';

        /**
         * Set Order By
         * @since 1.2 (before: OPTION_ORDER_BY)
         */
        const OrderBy = 'orderBy';

        /**
         * Set Custom Sql
         * @since 1.2 (before: OPTION_CUSTOM_SQL)
         */
        const CustomSql = 'customSql';

        /**
         * Use Specified Columns
         * @since 1.2 (before: OPTION_USE_COLUMNS)
         */
        const WithColumns = 'useColumns';

        /**
         * Disable limit & offset
         * @since 1.2 (before: OPTION_WITHOUT_PAGES)
         */
        const WithoutPages = 'withoutPages';

        /**
         * Return primary keys to object from Insert
         * @since 1.2 (required PostgreSQL 8.2)
         */
        const WithReturningKeys = 'withReturningKeys';

        /**
         * Default Cache Time in Seconds
         * @var int
         */
        public static $DefaultCacheTime = 3600;

        /**
         * Hooks
         * @var array
         */
        private static $hooks = array();

        /**
         * Cached Mappings
         * @var array
         */
        private static $mappings = array();

        /**
         * Cached Factories
         * @var IFactory[]
         */
        private static $factories = array();


        /**
         * Search Types array
         *
         * @var array
         */
        private static $searchTypes = array(
            SEARCHTYPE_EQUALS => array(
                'operator'       => '='
            )
            , SEARCHTYPE_ARRAY => array(
                'operator'       => 'IN'
            )
            , SEARCHTYPE_NOT_INARRAY => array(
                'operator'       => 'NOT IN'
            )
            , SEARCHTYPE_NOT_EQUALS => array(
                'operator'       => '!='
            )
            , SEARCHTYPE_NULL => array(
                'operator'       => ' IS NULL '
                , 'withoutValue' => true
            )
            , SEARCHTYPE_NOT_NULL => array(
                'operator'       => 'IS NOT NULL'
                , 'withoutValue' => true
            )
            , SEARCHTYPE_G => array(
                'operator'       => '>'
            )
            , SEARCHTYPE_GE => array(
                'operator'       => '>='
            )
            , SEARCHTYPE_L => array(
                'operator'       => '<'
            )
            , SEARCHTYPE_LE => array(
                'operator'       => '<='
            )
            , SEARCHTYPE_LIKE => array(
                'operator'       => 'LIKE'
                , 'appendLeft'   => '%'
                , 'appendRight'  => '%'
            )
            , SEARCHTYPE_ILIKE => array(
                'operator'       => 'ILIKE'
                , 'appendLeft'   => '%'
                , 'appendRight'  => '%'
            )
            , SEARCHTYPE_LEFT_LIKE => array(
                'operator'       => 'LIKE'
                , 'appendLeft'   => '%'
            )
            , SEARCHTYPE_RIGHT_LIKE => array(
                'operator'       => 'LIKE'
                , 'appendRight'  => '%'
            )
            , SEARCHTYPE_LEFT_ILIKE => array(
                'operator'       => 'ILIKE'
                , 'appendLeft'   => '%'
            )
            , SEARCHTYPE_RIGHT_ILIKE => array(
                'operator'       => 'ILIKE'
                , 'appendRight'  => '%'
            )
        );


        /**
         * Get Operator String
         *
         * @param string $operator  operator from SEARCHTYPE
         * @param string $field
         * @param string $value
         * @param string $type
         * @param string $complexType
         * @param IConnection $conn
         * @return string
         */
        public static function GetOperatorString( $operator, $field, $value = null, $type = TYPE_STRING, $complexType = null, $conn ) {
            if ( !empty( $complexType ) ) {
                $ct = $conn->GetComplexType( $complexType );
                if ( empty( $ct ) ) {
                    Logger::Error( 'Invalid complex type %s', $complexType );
                    return 'false';
                }

                return $ct->GetSearchOperatorString( $operator, $field, $value );
            } else if ( isset( self::$searchTypes[$operator] ) ) {
                $needValue = empty( self::$searchTypes[$operator]['withoutValue']) ;

                if ( $type == TYPE_DATE || $type == TYPE_TIME ) {
                    switch( strtolower(get_class($conn)) ) {
                        case 'mysqlconnection':
                        case 'mysqliconnection':
                            if ( $type == TYPE_DATE ) {
                                $field = 'date(' . $field . ')';
                            } else if ( $type == TYPE_TIME ) {
                                $field = 'time(' . $field . ')';
                            }
                            break;
                        case 'pgsqlconnection':
                            if ( $type == TYPE_DATE ) {
                                $field = $field . '::date';
                            } else if ( $type == TYPE_TIME ) {
                                $field = $field . '::time';
                            }
                            break;
                    }
                }

                if ( in_array( strtolower( get_class( $conn ) ), array( 'mysqlconnection', 'mysqliconnection' ) ) ) {
                    switch ( $operator ) {
                        case SEARCHTYPE_ILIKE:
                            $operator = SEARCHTYPE_LIKE;
                            break;
                        case SEARCHTYPE_LEFT_ILIKE:
                            $operator = SEARCHTYPE_LEFT_LIKE;
                            break;
                        case SEARCHTYPE_RIGHT_ILIKE:
                            $operator = SEARCHTYPE_RIGHT_LIKE;
                            break;
                    }
                }

                return $field . ' ' . self::$searchTypes[$operator]['operator'] . ' ' . ( $needValue ? $value : '' );
            }

            return $field . ' = ' . $value;
        }


        /**
         * Validate Object
         *
         * @param object $object   the object to be validated
         * @param array  $mapping  the object  mapping
         * @param array|null  $options
         * @param string $connectionName
         * @return array
         */
        public static function Validate( $object, array $mapping, $options, $connectionName = null ) {
            $conn   = ConnectionFactory::Get( $connectionName );
            $errors = array();

            if ( empty( $object ) )  {
                $errors['fatal'] = "null";
                return  $errors;
            }


            foreach ( $mapping['fields'] as $field => $data ) {
                $type = $data['type'];

                // set default values if null
                if ( isset( $data['default'] ) && is_null( $object->$field ) ) {
                    $object->$field = $data['default'];
                }

                // check for complex type
                if ( !empty( $data['complexType'] ) ) {
                    $ct = $conn->GetComplexType( $data['complexType'] );
                    if ( $ct !== null ) {
                        $error = $ct->Validate( $object->$field, $data, $options);
                        if ( !empty( $error ) ) {
                            $errors['fields'][$field] = $error;
                        }
                    }

                    continue;
                }

                // check for nullable
                if ( isset( $data['nullable'] ) ) {
                    switch ( $data['nullable']) {
                    	case 'CheckEmpty':
                    		if ( empty( $object->$field ) )  {
                    		    $errors['fields'][$field]['null'] = 'null';
                    		    continue 2;
                    		}

                    		break;
                    	case 'No':
                    	    if ( is_null( $object->$field ) ) {
                                $errors['fields'][$field]['null'] = 'null';
                    		    continue 2;
                    	    }

                    	    break;
                    	case 'Yes':
                    	case null:
                    	case 'null':
                    	case true:

                    	    break;
                    	default:
                    	    Logger::Error( 'Invalid nullable method for %s', $mapping['class']);
                    		break;
                    }
                }

                // check for min
                if ( !empty( $data['min'] ) ) {
                    switch ( $type ) {
                        case TYPE_STRING:
                            if ( BaseFactoryPrepare::StrLen( $object->$field ) < $data['min'] ) {
                                $errors['fields'][$field]['min'] = 'min';
                            }

                            break;
                        case TYPE_FLOAT:
                        case TYPE_INTEGER:
                            if ( $object->$field < $data['min'] ) {
                                $errors['fields'][$field]['min'] = 'min';
                            }
                            break;
                    	default:
                    	    Logger::Error( 'Invalid type %s for min check', $type );
                    		break;
                    }
                }

                // check for max
                if ( !empty( $data['max'] ) ) {
                    switch ( $type ) {
                        case TYPE_STRING:
                            if ( BaseFactoryPrepare::StrLen( $object->$field ) > $data['max'] ) {
                                $errors['fields'][$field]['max'] = 'max';
                            }

                            break;
                        case TYPE_FLOAT:
                        case TYPE_INTEGER:
                            if ( $object->$field > $data['max'] ) {
                                $errors['fields'][$field]['max'] = 'max';
                            }
                            break;
                    	default:
                    	    Logger::Error( 'Invalid type %s for min check', $type );
                    		break;
                    }
                }
            }

            return $errors;
        }


        /**
         * Validate Search Array
         * Fill from Mapping
         * Fill from Search Array
         *
         * @param array $searchArray   the search array
         * @param array $mapping       the object mapping
         * @param array|null $options
         * @param string $connectionName
         * @return array
         */
        public static function ValidateSearch( $searchArray, array $mapping, $options, $connectionName = null ) {
            $source        = $mapping["fields"];
            $resultSearch  = array();
            // Merge Search and Fields Array
            if ( !empty( $mapping["search"] ) ) {
                $source = array_merge_recursive( $source, $mapping["search"] );
            }

            // process array
            foreach ( $source as $field => $data ) {
                switch ($data["type"]) {
                	case TYPE_INTEGER:
                	case TYPE_FLOAT:
                    case TYPE_STRING:
                	case TYPE_BOOLEAN:
                	case TYPE_DATETIME:
                	case TYPE_DATE:
                	case TYPE_TIME:
                	    if ( !isset($searchArray[$field] ) ) {
                            if ( isset ($data["default"] ) ) {
                                $resultSearch[$field] = $data["default"];
                            } else {
                                $resultSearch[$field] = null;
                            }
                	    } else {
                	        $resultSearch[$field] = $searchArray[$field];

                            // string hack
                            if ( ( $data["type"] === TYPE_STRING ) &&
                                (( $searchArray[$field] == "" )
                                    || ($searchArray[$field] == "null" ))
                            ) {
                                $resultSearch[$field] = null;
                            }

                            // integer hack
                            if ( ( $data["type"] === TYPE_INTEGER ||  $data["type"] === TYPE_FLOAT ) && ( empty( $searchArray[$field] ) ) ) {
                                $resultSearch[$field] = null;
                            }

                            // date hack
                            if ( ( $data["type"] === TYPE_DATETIME ||  $data["type"] === TYPE_DATE )
                                    && ( empty( $searchArray[$field] )
                                        || ( is_string($searchArray[$field])
                                            && (trim($searchArray[$field]) == "")
                                         )
                                     )
                                 )
                            {
                                $resultSearch[$field] = null;
                            }
                	    }

                		break;
                	case TYPE_ARRAY:
                	    break;
                	default:
                	    Logger::Error(  'Unknown type for search %s', $data["type"] );
                		break;
                }
            }

            // Pages Hack for GetAction
            if ( !isset( $resultSearch[BaseFactoryPrepare::PageSize] ) ) {
                $resultSearch[BaseFactoryPrepare::PageSize] = BaseFactoryPrepare::PageSizeCount;
            }

            if ( !isset( $resultSearch[BaseFactoryPrepare::Page] ) ) {
                $resultSearch[BaseFactoryPrepare::Page] = 0;
            }

            return $resultSearch;
        }


        /**
         * Update Object
         *
         * @param object  $object          the object
         * @param array   $mapping         the object mapping
         * @param array $options
         * @param string  $connectionName  the database connection name
         * @return array|boolean
         */
        public static function Update( $object, array $mapping, $options, $connectionName = null ) {
            $errors = BaseFactory::Validate( $object, $mapping, $options, $connectionName );

            if ( !empty( $errors ) ) {
                return false;
            }

            $conn   = ConnectionFactory::Get( $connectionName );
            $cmd    = new SqlCommand( BaseFactoryPrepare::PrepareUpdateStatement( $mapping, $conn ), $conn );
            $hasKey = false;

            foreach ( $mapping["fields"] as $field => $data ) {
                if ( !BaseFactoryPrepare::CheckUpdatable( $data ) ) {
                    continue;
                }

                if ( !$hasKey  && !empty($data["key"]) && !is_null( $object->$field )  ) {
                    $hasKey = true;
                }

                $cmd->SetParameter( "@" . $field , $object->$field, $data["type"], isset( $data['complexType'] ) ? $data['complexType'] : null  );
            }

            //TODO: Addcheck for LIST types
            if ( $hasKey ) {
                self::MarkCacheAsOld( $mapping );

                $result = $cmd->ExecuteNonQuery();

            } else {
                Logger::Error( '%s has no primary key', $mapping['class']);
                return false;
            }

            self::callHook(__FUNCTION__, $mapping);

            return $result;
        }


        /**
         * Update array of objects
         *
         * @param array  $objects
         * @param array  $mapping
         * @param array  $options
         * @param string $connectionName
         * @return result
         */
        public static function UpdateRange( $objects, array $mapping, $options, $connectionName = null ) {
            $conn   = ConnectionFactory::Get( $connectionName );
            $cmd    = new SqlCommand( BaseFactoryPrepare::PrepareUpdateStatement( $mapping, $conn ), $conn );
            $result = null;

            /// foreach objects
            foreach ( $objects as $object ) {
                $hasKey = false;
                $errors = self::Validate( $object, $mapping, $options, $connectionName );

                if ( !empty( $errors ) ) {
                    return false;
                }

                // set parameters
                foreach ( $mapping["fields"] as $field => $data ) {
                    if ( BaseFactoryPrepare::checkUpdatable( $data ) ) {
                        $cmd->SetParameter( "@" . $field , $object->$field, $data["type"], isset( $data['complexType'] ) ? $data['complexType'] : null  );
                    }


                    if ( !$hasKey  && !empty($data["key"]) && !is_null( $object->$field )  ) {
                        $hasKey = true;
                    }
                }

                //TODO: Addcheck for LIST types

                // execute query
                if ( $hasKey ) {
                    $result = $cmd->ExecuteNonQuery();
                } else {
                    Logger::Error( '%s has no primary key', $mapping['class']);
                    return false;
                }
            }

            self::MarkCacheAsOld( $mapping );
            self::callHook(__FUNCTION__, $mapping);

            return $result;
        }


        /**
         * Add Object
         *
         * @param object  $object          the object
         * @param array   $mapping         the object mapping
         * @param array|null   $options
         * @param string  $connectionName  the database connection name
         * @return array|boolean
         */
        public static function Add( $object, array $mapping, $options, $connectionName = null ) {
            $errors = BaseFactory::Validate( $object, $mapping, $options, $connectionName );

            if ( !empty( $errors ) ) {
                return false;
            }

            $conn = ConnectionFactory::Get( $connectionName );
            $cmd  = new SqlCommand( BaseFactoryPrepare::PrepareAddStatement( $mapping, $options, $conn, $object ), $conn );

            foreach ( $mapping['fields'] as $field => $data ) {
                if ( !BaseFactoryPrepare::CheckAddable( $data ) ) {
                    continue;
                }

                $cmd->SetParameter( '@' . $field , $object->$field, $data['type'], isset( $data['complexType'] ) ? $data['complexType'] : null );
            }

            // with returning keys
            $result = false;
            if ( !empty( $options ) && !empty( $options[self::WithReturningKeys ] ) ) {
                switch( $conn->GetClassName() ) {
                    case 'PgSqlConnection':
                        $ds = $cmd->Execute();
                        if ( $ds->next() ) {
                            $key          = ArrayHelper::GetFirstElement( BaseFactoryPrepare::GetPrimaryKeys( $mapping ) );
                            $object->$key = $ds->GetParameter( $key );
                            $result       = true;
                        }
                        break;
                    case 'MySqlConnection':
                    case 'MySqliConnection':
                        $ds           = $cmd->Execute();
                        $key          = ArrayHelper::GetFirstElement( BaseFactoryPrepare::GetPrimaryKeys( $mapping ) );
                        $object->$key = $conn->GetLastInsertId();
                        $result       = true;
                        break;
                }
            } else {
                $result = $cmd->ExecuteNonQuery();
            }
            // eof returning keys

            //TODO: Add check for LIST types
            self::MarkCacheAsOld( $mapping );
            self::callHook(__FUNCTION__, $mapping );

            return $result;
        }


        /**
         * Add array of objects
         *
         * @param array  $objects
         * @param array  $mapping
         * @param array  $options
         * @param string $connectionName
         * @return bool
         */
        public static function AddRange( $objects, array $mapping, $options, $connectionName = null ) {
            $conn   = ConnectionFactory::Get( $connectionName );
            $cmd    = new SqlCommand(  "", $conn );
            $result = null;

            /// foreach objects
            foreach ( $objects as $object ) {
                $errors = BaseFactory::Validate( $object, $mapping, $options, $connectionName );

                if ( !empty( $errors ) ) {
                    return false;
                }

                // TODO REFACTOR
                $cmd->SetCommand( BaseFactoryPrepare::PrepareAddStatement( $mapping, $options, $conn, $object ) );
                $cmd->ClearParameters();

                // set parameters
                foreach ( $mapping["fields"] as $field => $data ) {
                    if ( BaseFactoryPrepare::CheckAddable( $data ) ) {
                        $cmd->SetParameter( "@" . $field , $object->$field, $data["type"], isset( $data['complexType'] ) ? $data['complexType'] : null );
                    }
                }

                //TODO: Add Check for LIST types
                $result = $cmd->ExecuteNonQuery();
            }

            self::callHook(__FUNCTION__, $mapping);
            self::MarkCacheAsOld( $mapping );

            return $result;
        }


        /**
         * Can Pages?
         *
         * @param array $mapping
         * @param array $options
         * @return unknown
         */
        public static function CanPages( array $mapping, $options = null ) {
            if ( isset( $mapping['flags']['CanPages'] )
                && empty( $options[BaseFactory::WithoutPages]) ) {
                return true;
            }

            return false;
        }


        /**
         * Count Pages
         *
         * @param array $searchArray
         * @param array $mapping
         * @param $options
         * @param array $connectionName
         * @return integer
         */
        public static function Count( &$searchArray, array $mapping, $options, $connectionName = null ) {
            $conn      = ConnectionFactory::Get( $connectionName );
            $cmd       = new SqlCommand( BaseFactoryPrepare::PrepareCountString( $searchArray, $mapping, $options, $conn ), $conn );
            $cacheKey  = '';
            $timestamp = time();
            $tags      = array();
            $count     = null;

            if( !empty($options[BaseFactory::CacheTime] ) ) {
                $expires = $options[BaseFactory::CacheTime];
            } else {
                $expires = empty( $mapping['cache'] ) ? self::$DefaultCacheTime : $mapping['cache'];
            }
            

            self::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );

            // Check Reslts in Cache
            if ( !empty( $mapping['flags']['CanCache'] ) && MemcacheHelper::IsActive() && empty( $options[BaseFactory::WithoutCache] ) ) {
                $cacheKey    = $mapping['class'] . '_query_' . md5( $cmd->GetQuery() );
                $cacheResult = MemcacheHelper::Get( $cacheKey );

                if ( $cacheResult !== false
                    && $timestamp > $cacheResult['time'] - $expires )
                {
                    $tags = MemcacheHelper::Get( BaseFactoryPrepare::GetCacheTags( $mapping ) );
                    if ( MemcacheHelper::CompareTags( $cacheResult['tags'], $tags ) ) {
                        $count = $cacheResult['data'];
                    }
                }
            }


            if ( is_null( $count ) ) {
                $ds = $cmd->execute();
                if ( $ds->next() ) {
                    $count = $ds->getInteger( 'count' );
                } else {
                    $count = 0;
                }

                // if value is null
                if ( is_null( $count ) ) {
                    $count = 0;
                }
            }

            // Cache Results
           if ( !empty( $mapping['flags']['CanCache'] ) && MemcacheHelper::IsActive() && empty( $options[BaseFactory::WithoutCache] ) ) {
               if ( empty( $tags ) ) {
                   $tags = MemcacheHelper::Get( BaseFactoryPrepare::GetCacheTags( $mapping ) );
               }

               BaseFactoryPrepare::SaveCacheResult( $cacheKey, $tags, $count, $expires, $timestamp );
            }
            // OEF memcache

            // Calculate Page Count
            if ( !self::CanPages( $mapping, $options ) ) {
                $searchArray[BaseFactoryPrepare::PageSize] = 1;
            }

            if ( 0 == $searchArray[BaseFactoryPrepare::PageSize] ) {
                $searchArray[BaseFactoryPrepare::PageSize] = BaseFactoryPrepare::PageSizeCount;
            }

            return ( $count / $searchArray[BaseFactoryPrepare::PageSize] );
        }


        /**
         * Get Structure for Parsing
         *
         * @param array $columns
         * @return array
         */
        public static function GetObjectTree( array $columns ) {
            $tree = array();

            foreach ( $columns as $name => $type ) {
                ArrayHelper::SetValue( $tree, explode( '.', $name ), $type );
            }

            return $tree;
        }


        /**
         * Get Object from Row
         *
         * @param DataSet   $ds
         * @param array     $mapping
         * @param array $tree
         * @param string $prefix
         *
         * @return object
         */
        public static function GetObject( $ds, array $mapping, array $tree, $prefix = '' ) {
            if ( !empty( $mapping['flags']['IsTree'] ) ) {
                $mapping['fields'] = array_merge( $mapping['fields'], BaseTreeFactory::$mapping['fields'] );
            }

            $result = new $mapping['class']();

            foreach ( $tree as $field => $value ) {
                $factoryName = null;
                
                if ( false === is_array( $value ) ) {
                    if ( !empty( $mapping['fields'][$field] ) ) {
                        $type        = $mapping['fields'][$field]['type'];
                        $complexType = ArrayHelper::GetValue( $mapping['fields'][$field], 'complexType' );

                        if ( !empty( $complexType ) ) {
                            $result->$field = $ds->GetComplexType( $prefix . $field, $complexType );
                        } else {
                            $result->$field = $ds->GetValue( $prefix . $field, $type );
                        }
                    }
                } else if ( !empty( $mapping['fields'][$field . 'Id'] ) ) { 
                    $factoryName = $mapping['fields'][$field . 'Id']['foreignKey'] . 'Factory';
                } else if ( !empty( $mapping['fields'][$field . '_id'] ) ) {
                    $factoryName = $mapping['fields'][$field . '_id']['foreignKey'] . 'Factory';
                }

                if ( $factoryName !== null ) {
                    $result->$field = self::GetObject( $ds, self::GetMapping( $factoryName ), $value, $prefix . $field . '.'  );
                }
                
            }

            return $result;
        }


        /**
         * Process Search Parameters
         *
         * @param array      $searchArray
         * @param array      $mapping
         * @param $options
         * @param SqlCommand $cmd
         */
        public static function ProcessSearchParameters( $searchArray = array(), array $mapping, $options, $cmd ) {
            if ( empty( $searchArray ) ) {
                $searchArray = array();
            }

            foreach ( $searchArray as $field => $value ) {
                if ( is_null( $value ) || $field == BaseFactoryPrepare::Page  || $field == BaseFactoryPrepare::PageSize ) {
                    continue;
                }

                // Get Type And Search Type
                if ( isset( $mapping['fields'][$field] )  ) {
                    $type        = $mapping['fields'][$field]['type'];
                    $complexType = ArrayHelper::GetValue( $mapping['fields'][$field], 'complexType' );
                    $searchType  = ( isset($mapping['fields'][$field]['searchType'] ) ? $mapping['fields'][$field]['searchType'] : SEARCHTYPE_EQUALS);
                } elseif ( isset( $mapping['search'][$field] ) ) {
                    $type        = $mapping['search'][$field]['type'];
                    $complexType = ArrayHelper::GetValue( $mapping['search'][$field], 'complexType' );
                    $searchType  = ( isset($mapping['search'][$field]['searchType'] ) ? $mapping['search'][$field]['searchType'] : SEARCHTYPE_EQUALS);
                } else {
                    continue;
                }

                $appendLeft  = ( !empty( self::$searchTypes[$searchType]['appendLeft'] ) ) ? self::$searchTypes[$searchType]['appendLeft'] : '';
                $appendRight = ( !empty( self::$searchTypes[$searchType]['appendRight'] ) ) ? self::$searchTypes[$searchType]['appendRight'] : '';

                if ( isset( $type ) ) {
                    if ( $searchType == SEARCHTYPE_ARRAY || $searchType == SEARCHTYPE_NOT_INARRAY ) {
                        $cmd->SetList( '@' . $field, $value, $type );
                    } else {
                        if ( $type == TYPE_STRING ) {
                            $value = $appendLeft . $value . $appendRight;
                        }

                        $cmd->SetParameter( '@' . $field , $value, $type, $complexType );
                    }
                }
            }

            if ( !isset($options[BaseFactory::WithoutDisabled] ) ) {
                $options[BaseFactory::WithoutDisabled] = true;
            }

            if ( !empty( $options[BaseFactory::WithoutDisabled] ) ) {
                $cmd->SetInteger( '@eaze_' . BaseFactory::WithoutDisabled, 2 );
            }
        }

        
        /**
         * Get Foreign Lists
         * @param array $options          options array
         * @param array $mapping          object mapping
         * @param array $data             list of objects
         * @param string $connectionName  connection name
         */
        private static function getLists( $options, $mapping, &$data, $connectionName ) {
            if ( !empty( $options[BaseFactory::WithLists] )
                    && !empty( $mapping['lists'] )
                    && !empty( $data ) )
            {
                foreach ( $mapping['lists'] as $name => $value ) {
                    $ids       = array_keys( $data );
                    $factory   = self::GetInstance( $value['foreignKey'] . 'Factory' );
                    $listArray = $factory->Get( array( '_' . $value['name'] => $ids ), array( BaseFactory::WithoutPages => true), $connectionName );
                    BaseFactoryPrepare::Glue( $data, $listArray, $value['name'], $name );
                }
            }
        }


        /**
         * Get
         *
         * @param array $searchArray
         * @param array $mapping
         * @param array $options
         * @param array $connectionName
         * @return array
         */
        public static function Get( $searchArray, array $mapping, $options = null, $connectionName = null ) {
            $conn        = ConnectionFactory::Get( $connectionName );
            $cmd         = new SqlCommand( BaseFactoryPrepare::PrepareGetString( $searchArray, $mapping, $options, $conn ), $conn );
            $cacheKey    = "";
            $timestamp   = time();
            $tags        = array();
            if( !empty($options[BaseFactory::CacheTime] ) ) {
                $expires = $options[BaseFactory::CacheTime];
            } else {
                $expires = empty( $mapping["cache"] ) ? self::$DefaultCacheTime : $mapping["cache"];
            }
            
            self::ProcessSearchParameters( $searchArray, $mapping, $options, $cmd );

            if ( self::CanPages( $mapping ) ) {
                $cmd->SetInteger( "@pageOffset", $searchArray[BaseFactoryPrepare::Page] * $searchArray[BaseFactoryPrepare::PageSize] );
                $cmd->SetInteger( "@pageSize",   $searchArray[BaseFactoryPrepare::PageSize] );
            }

            $regenerateCache = false;
            if( ! empty( $mapping['flags']['CanCache'] )
                    && MemcacheHelper::IsActive()
                    && empty( $options[BaseFactory::WithoutCache] ) ) {

                $cacheKey    = $mapping['class'] . '_query_' . md5( $cmd->GetQuery() );
                $cacheResult = MemcacheHelper::Get( $cacheKey );

                if( $cacheResult !== false && $timestamp > ( $cacheResult['time'] - $expires ) ) {
                    $tags = MemcacheHelper::Get( BaseFactoryPrepare::GetCacheTags( $mapping ) );

                    if ( MemcacheHelper::CompareTags( $cacheResult['tags'], $tags ) ) {
                        self::getLists( $options, $mapping, $cacheResult['data'], $connectionName );
                        return $cacheResult['data'];
                    }
                }

                $regenerateCache = MemcacheHelper::AddBlock( $cacheKey );
                if( $regenerateCache == false && $cacheResult == false ) {
                    sleep( 0.1 );
                    // TODO rewrite
                }
                $cacheResult  = MemcacheHelper::Get( $cacheKey );

                if( $regenerateCache == false && $cacheResult !== false ) {
                    $tags = MemcacheHelper::Get( BaseFactoryPrepare::GetCacheTags( $mapping ) );

                    if ( MemcacheHelper::CompareTags( $cacheResult['tags'], $tags ) ) {
                        self::getLists( $options, $mapping, $cacheResult['data'], $connectionName );
                        return $cacheResult['data'];
                    }
                }
            }




            $ds        = $cmd->Execute();
            $structure = self::GetObjectTree( $ds->Columns );
            $result    = array();
            $keys      = BaseFactoryPrepare::GetPrimaryKeys( $mapping );
            $key       = ( count($keys) == 1 ) ? $keys[0] : null;

            while ( $ds->next() ) {
                if ( !empty($structure[$key])) {
                    $result[$ds->getParameter( $key )] = self::getObject( $ds, $mapping, $structure );
                } else {
                    $result[] = self::getObject( $ds, $mapping, $structure );
                }
            }

            // With Lists Mode
            self::getLists( $options, $mapping, $result, $connectionName );

            // memcached support
            if ( !empty( $mapping["flags"]["CanCache"] )
                    && MemcacheHelper::IsActive()
                    && empty( $options[BaseFactory::WithoutCache] ) ) {
                if ( empty( $tags ) ) {
                    $tags = MemcacheHelper::Get( BaseFactoryPrepare::GetCacheTags( $mapping ) );
                }

                BaseFactoryPrepare::SaveCacheResult( $cacheKey, $tags, $result, $expires, $timestamp );
                if( $regenerateCache == true ) {
                    MemcacheHelper::DeleteBlock( $cacheKey );
                }
            }
            // OEF memcache

            return $result;
        }


        /**
         * Physical Delete Object
         *
         * @param array  $mapping
         * @param object $object
         * @param string $connectionName
         * @return boolean
         */
        public static function PhysicalDelete( $object, array $mapping, $connectionName = null ) {
            if ( empty( $object ) ) {
                return false;
            }

            $conn = ConnectionFactory::Get( $connectionName );

            if ( is_null( $conn ) ) {
                Logger::Error( 'Could not obtain connection named %s', $connectionName );
            }

            $cmd = new SqlCommand( BaseFactoryPrepare::prepareDeleteString( $mapping, $conn ) , $conn );

            foreach ( $mapping["fields"] as $name => $info ) {
                if ( false == empty( $info["key"] ) ) {
                    if ( $info["key"] ) {
                        $cmd->SetParameter( "@" . $name, $object->$name, $info["type"] );
                    }
                }
            }

            self::MarkCacheAsOld( $mapping );

            $result = $cmd->ExecuteNonQuery();

            self::callHook(__FUNCTION__, $mapping);

            return $result;
        }


        /**
         * Logical Delete
         *
         * @param array  $mapping
         * @param object $object
         * @param string $connectionName
         * @return boolean
         */
        public static function LogicalDelete( $object, array $mapping, $connectionName = null ) {
            if ( empty( $object ) ) {
                return false;
            }

            $canBeUpdated = false;
            foreach ( $mapping["fields"] as $field => $data ){
                if ( $field == "statusId" ) {
                    $canBeUpdated = true;
                    break;
                }
            }

            if ( $canBeUpdated ) {
                $object->statusId = 3;
                self::Update( $object, $mapping, $connectionName );
                return true;
            }

            return false;
        }


        /**
         * Delete Object
         *
         * @param object $object
         * @param array  $mapping
         * @param string $connectionName
         * @return bool
         */
        public static function Delete( $object, array $mapping, $connectionName = null ) {
            if ( !self::LogicalDelete( $object, $mapping, $connectionName ) ) {
                return self::PhysicalDelete( $object, $mapping, $connectionName );
            }

            return true;
        }


        /**
         * Get One
         *
         * @param array  $searchArray
         * @param array  $mapping
         * @param array  $options
         * @param string $connectionName
         * @return mixed
         */
        public static function GetOne( $searchArray, array $mapping, $options = null, $connectionName = null ) {
            $result = self::Get( $searchArray, $mapping, $options, $connectionName );

            if ( empty( $result ) || count( $result ) != 1  ) {
                return null;
            }

            if ( !is_null( $result ) ) {
                return reset( $result );
            }

            return null;
        }


        /**
         * Get Current Id (returns max( id ) )
         *
         * @param array $mapping
         * @param string $connectionName optional
         *
         * @return int
         */
        public static function GetCurrentId( array $mapping, $connectionName = null ) {
            $conn = ConnectionFactory::Get( $connectionName );
            $cmd  = new SqlCommand( BaseFactoryPrepare::PrepareGetCurrentIdString( $mapping, $conn ), $conn );
            $ds = $cmd->Execute();

            $key = null;
            if ( $ds->next() ) {
                $key = $ds->getInteger( 'key' );
            }

            if ( is_null( $key ) ) {
                $key = 0;
            }

            return $key;
        }


        /**
         * Get By Id
         *
         * @param mixed  $id
         * @param array  $searchArray
         * @param array  $mapping
         * @param array $options
         * @param string $connectionName
         * @return mixed|null
         */
        public static function GetById( $id, $searchArray, array $mapping, $options, $connectionName = null ) {
            if (  is_null( $id ) ) {
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

            return self::GetOne( $searchArray, $mapping, $options, $connectionName );
        }


        /**
         * Update By Mask
         *
         * @param object $object
         * @param array $changes
         * @param array $searchArray
         * @param array $mapping
         * @param string $connectionName
         * @return bool
         */
        public static function UpdateByMask( $object, $changes, $searchArray, array $mapping, $connectionName = null ) {
            if ( empty( $changes ) ) {
                return false;
            }

            $errors = BaseFactory::Validate( $object, $mapping, null, $connectionName );
            if ( !empty( $errors["fields"] ) ) {
                foreach ( $errors["fields"] as $field => $value ) {
                    if ( !in_array( $field, $changes ) ) {
                       unset( $errors["fields"][$field] );
                    }
                }
            }

            if ( !empty( $errors["fields"] ) ) {
                return $errors;
            }

            $conn        = ConnectionFactory::Get( $connectionName );
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, null, $connectionName );
            $cmd         = new SqlCommand( BaseFactoryPrepare::PrepareUpdateByMaskStatement( $searchArray, $changes, $mapping, $conn ), $conn );

            foreach ( $mapping["fields"] as $field => $data ) {
                if ( !BaseFactoryPrepare::CheckUpdatable( $data ) ) {
                    continue;
                }

                if ( !in_array( $field, $changes ) ) {
                    continue;
                }

                $cmd->SetParameter( "@update_" . $field , $object->$field, $data["type"], isset( $data['complexType'] ) ? $data['complexType'] : null );
            }

            self::ProcessSearchParameters( $searchArray, $mapping, array(BaseFactory::WithoutDisabled=>false), $cmd );
            self::MarkCacheAsOld( $mapping );

            $result = $cmd->ExecuteNonQuery();

            self::callHook(__FUNCTION__, $mapping);

            return $result;
        }


        /**
         * Delete By Mask
         *
         * @param array $searchArray
         * @param array $mapping
         * @param string $connectionName
         * @return bool
         */
        public static function DeleteByMask( $searchArray, array $mapping, $connectionName = null ) {
            if ( empty( $searchArray ) ) {
                return false;
            }

            $conn        = ConnectionFactory::Get( $connectionName );
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, null, $connectionName );
            $cmd         = new SqlCommand( BaseFactoryPrepare::PrepareDeleteByMaskStatement( $searchArray, $mapping, $conn ), $conn );

            self::ProcessSearchParameters( $searchArray, $mapping, array(BaseFactory::WithoutDisabled=>false), $cmd );
            self::MarkCacheAsOld( $mapping );

            $result = $cmd->ExecuteNonQuery();

            self::callHook(__FUNCTION__, $mapping);

            return $result;
        }


        /**
         * Save Array
         *
         * @param array $objects
         * @param array $originalObjects
         * @param array $mapping
         * @param string $connectionName
         * @return array or bool
         */
        public static function SaveArray( $objects, $originalObjects = null, array $mapping, $connectionName = null )  {
            $errors  = array();
            $factory = self::GetInstance( $mapping['class'] . 'Factory' );

            // Check For Errors
            $hasErrors = false;
            if ( empty(  $objects ) ) {
                if ( ! empty( $originalObjects ) ) {
                    foreach ( $originalObjects as $oo ) {
                        $factory->Delete( $oo, $connectionName );
                    }
                }

                return true;
            }


            foreach ( $objects as $o  ) {
                $error    = $factory->Validate( $o, null, $connectionName );
                if ( !empty( $error ) ) {
                    $hasErrors = true;
                }

                $errors[] = $error;
            }

            if ( $hasErrors ) {
                return $errors;
            }

            // Fill Add/Update/Delete Arrays
            $forAdd    = $forUpdate = array();
            $keys      = BaseFactoryPrepare::GetPrimaryKeys( $mapping );

            foreach ( $objects as $o ) {
                // Detect Mode
                $mode = 'update';
                foreach ( $keys as $key ) {
                    if ( empty( $o->$key ) ) {
                        $mode = 'add';
                        break;
                    }
                }

                if ( $mode == 'add' ) {
                    $forAdd[] = $o;
                } elseif ( $mode == 'update' ) {
                    $forUpdate[] = $o;
                }
            }

            $result1 = $result2 = true;
            // Begin Add
            if ( !empty( $forAdd ) ) {
                $result1 = $factory->AddRange( $forAdd, $connectionName );
            }
            // Begin Update
            if ( !empty( $forUpdate ) ) {
                // Begin Delete Old Objects
                if  ( !empty($originalObjects) ) {
                    foreach ( $originalObjects  as $oo ) {
                        // if not in newIds then delete
                        $delete = true;

                        // not in condition
                        foreach ( $forUpdate as $o ) {
                            foreach ( $keys as $key ) {
                                if ( $o->$key == $oo->$key) {
                                    $delete = false;
                                    break 2;
                                }
                            }
                        }

                        if ( $delete ) {
                            $factory->Delete( $oo, $connectionName );
                        }
                    }
                }

                $result2 = $factory->UpdateRange( $forUpdate, $connectionName );
            } else if ( empty( $forUpdate ) && !empty( $originalObjects ) ) {
                foreach ( $originalObjects as $oo ) {
                    $result2 = $factory->Delete( $oo );
                }
            }

            return ( $result1 & $result2 );
        }



        /**
         * Get Object From Request
         *
         * @param string $prefix            the array prefix (default is classname)
         * @param array $mapping
         * @param mixed $value
         * @param string $connectionName
         * @return object
         */
        public static function GetFromRequest( $prefix = null, array $mapping, $value = null, $connectionName = null ) {
            static $listObjects = array();

            $object = new $mapping["class"]();

            if ( empty( $value ) ) {
                $prefix  = empty( $prefix ) ? $mapping["class"] : $prefix;
                $prefix  = strtolower( $prefix[0] ) . substr( $prefix, 1, strlen( $prefix ) );
                $request = Request::getArray( $prefix );
            } else {
                $request = $value;
            }

            foreach ( $mapping['fields'] as $field => $data ) {
                // request boolean hack
                if ( $data['type'] === TYPE_BOOLEAN && empty( $request[$field] ) ) {
                    $request[$field] = false;
                }

                // request datetime hack
                if ( ( $data['type'] === TYPE_DATETIME  || $data['type'] === TYPE_DATE )
                    && ( empty( $request[$field] ) ))
                {
                    unset( $request[$field]);
                }

                if ( isset( $request[$field] ) ) {
                    // check for complex type
                    if ( !empty( $data['complexType'] ) ) {
                        $ct = ConnectionFactory::Get( $connectionName )->GetComplexType( $data['complexType'] );
                        if ( $ct !== null ) {
                            $object->$field = $ct->FromRequest( $request[$field] );
                        }

                        continue;
                    }

                    // get simple value
                    $object->$field = Convert::ToValue( $request[$field], $data["type"] );

                    if ( !empty( $data["foreignKey"] ) && !empty( $object->$field ) ) {
                        $fkField   = substr( $field, 0,  strlen( $field ) - 2 ); // created from fkId - 2 chars
                        $fkFactory = self::GetInstance( $data['foreignKey'] . 'Factory' );

                        // get from cache
                        if ( array_key_exists( $data['foreignKey'], $listObjects )
                             && array_key_exists( $object->$field,  $listObjects[$data['foreignKey']] ) )
                        {
                            $object->$fkField = $listObjects[$data['foreignKey']][$object->$field];
                        } else {
                            $object->$fkField = $fkFactory->GetById( $object->$field, array(), array( BaseFactory::WithoutDisabled => false ), $connectionName );
                        }

                        if ( empty ( $object->$fkField ) ) {
                            $object->$field = null;
                        }
                    }
                }
            }

            $listObjects = array_merge( $listObjects, self::getListObjectsFromRequest( $mapping['lists'], $request, $connectionName ) );
            foreach ( $mapping["lists"] as $field => $value ) {
                if ( !empty( $request[$field] ) ) {
                    $fMapping = self::GetMapping( $value["foreignKey"] . 'Factory' );

                    foreach ( $request[$field] as $arrValue ) {
                        $a = $object->$field;
                        $a[]  = self::GetFromRequest( null, $fMapping, $arrValue );
                        $object->$field = $a;
                    }
                }
            }

            return $object;
        }


        /**
         * Get ListObjects for GetFromRequest
         * @static
         * @see GetFromRequest
         * @param  array  $lists            mapping[lists]
         * @param  array  $request          request::getArray()
         * @param  string $connectionName
         * @return array columns (foreignKey => array( id => object ) )
         */
        private static function getListObjectsFromRequest( $lists, $request, $connectionName = null ) {
            $fkTypes = array();
            $result  = array();
            $fkIds   = array();

            if ( empty( $lists ) ) {
                return $result;
            }

            foreach ( $lists as $listField => $listValue ) {
                if ( empty( $request[$listField] ) ) {
                    continue;
                }

                // Find all FK on depend objects
                $mapping = self::GetMapping( $listValue['foreignKey'] . 'Factory' );
                foreach ( $mapping['fields'] as $value  ) {
                    $fk = ArrayHelper::GetValue( $value, 'foreignKey' );
                    if ( $fk === null ) {
                        continue;
                    }

                    $fkMapping     = BaseFactory::GetMapping( $fk . 'Factory' );
                    $fkPrimaryKeys = BaseFactory::GetPrimaryKeys( $fkMapping );
                    if ( count( $fkPrimaryKeys ) == 1 ) {
                        $fkValue      = current( $fkPrimaryKeys );
                        $fkTypes[$fk] = array( $fkValue['name'], $fkValue['type'], $value['name'] );
                    }
                }

                // fill Ids from request
                foreach( $request[$listField] as $arrValue ) {
                    foreach( $fkTypes as $fkType => $fkKey ) {
                        $fkValue        = ArrayHelper::GetValue( $arrValue, $fkKey[2] );
                        if ( $fkValue !== null ) {
                            $fkIds[$fkType][] = $fkValue;
                        }
                    }
                }
            }

            // fill request
            $conn = ConnectionFactory::Get( $connectionName );
            foreach( $fkIds as $fkType => $value ) {
                if ( empty( $value ) ) {
                    continue;
                }

                $factory = self::GetInstance( $fkType . 'Factory' );
                $fkId    = $fkTypes[$fkType];
                $sql     = sprintf( ' AND %s IN %s ', $conn->Quote( $fkId[0] ), $conn->GetConverter()->ToList( $value, $fkId[1] ) );

                $result[$fkType] = $factory->Get( array(), array( BaseFactory::CustomSql => $sql, BaseFactory::WithoutPages => true ), $connectionName );
                // fill with unused values
                foreach( $value as $val ) {
                    if ( empty( $result[$fkType][$val] ) ) {
                        $result[$fkType][$val] = null;
                    }
                }
            }

            return $result;
        }


        /**
         * Increment Master Tag Value
         *
         * @param array $mapping
         * @return bool
         */
        public static function MarkCacheAsOld( array $mapping ) {
            $result = MemcacheHelper::Increment( $mapping["table"] );

            if ( empty( $result ) ) {
                MemcacheHelper::AddValue( $mapping["table"], 1, 0, 0 );
            }

            return true;
        }

        /**
         * Gets array of the primary keys of the object.
         *
         * @param array $mapping
         * @return array
         */
        public static function GetPrimaryKeys( $mapping ) {
            $result = array();

            foreach ( $mapping['fields'] as $value ) {
                if ( !empty( $value['key'] ) ) {
                    $result[] = $value;
                }
            }

            return $result;
        }


        /**
         * Get Cached Factory
         * @static
         * @param  string $factoryName
         * @return IFactory
         */
        public static function GetInstance( $factoryName ) {
            if ( !array_key_exists( $factoryName,  self::$factories ) ) {
                self::$factories[$factoryName] = new $factoryName;
            }

            return self::$factories[$factoryName];
        }


        /**
         * Get Cached Factory Mapping
         * @static
         * @param  string $factoryName
         * @return array mapping
         */
        public static function GetMapping( $factoryName ) {
            if ( empty( self::$mappings[$factoryName] ) ) {
                $vars = get_class_vars( $factoryName );
                self::$mappings[$factoryName] = $vars['mapping'];
            }

            return self::$mappings[$factoryName];
        }


        /**
         * Register Hook
         * @static
         * @param  string   $methodName
         * @param  callback $callback
         * @return void
         */
        public static function RegisterHook( $methodName, $callback ) {
            self::$hooks[$methodName][] = $callback;
        }


        /**
         * Call Hook
         * @static
         * @param  $methodName
         * @param  $argument
         * @return void
         */
        private static function callHook( $methodName, $argument ) {
            if (!empty(self::$hooks[$methodName]) ) {
                foreach( self::$hooks[$methodName] as $hook ) {
                    call_user_func( $hook, $argument );
                }
            }
        }
    }
?>