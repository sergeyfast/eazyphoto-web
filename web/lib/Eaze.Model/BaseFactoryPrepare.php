<?php
    /**
     * BaseFactory Prepare
     * @package Eaze
     * @subpackage Model
     */
    class BaseFactoryPrepare {

        /**
         * Default Page Parameter
         */
        const Page = 'page';

        /**
         * Default Page Size Parameter
         */
        const PageSize = 'pageSize';

        /**
         * Default branchId Field
         */
        const BranchId = 'branchId';

        /**
         * Status Id Field
         */
        const StatusId = 'statusId';

        /**
         * Page Size Value
         */
        const PageSizeCount = 10;

        /**
         * Tags Cache
         * @var array
         */
        private static $tagsCache = array();



        /**
         * Check FieldData for Add
         *
         * @param array $data
         * @return boolean
         */
        public static function CheckAddable( array $data ) {
            if ( isset( $data['addable'] ) && $data['addable'] === false ) {
                return false;
            }

            return true;
        }

        /**
         * Check FieldData for Update
         *
         * @param array $data
         * @return boolean
         */
        public static function CheckUpdatable( array $data ) {
            if ( isset( $data['updatable'] ) && $data['updatable'] === false ) {
                return false;
            }

            return true;
        }


        /**
         * Prepare Add statement string
         *
         * @param array  $mapping
         * @param array $options
         * @param IConnection $conn
         * @param object $object
         * @return string
         */
        public static function PrepareAddStatement( array $mapping, $options = array(), IConnection $conn, $object = null  ) {
            // detect autoincrement fields
            $keys = BaseFactoryPrepare::GetPrimaryKeys( $mapping );
            $pk   = null;
            if ( count( $keys ) > 1 ) {
                $isSerial = false;
            } else {
                $isSerial = false;
                if ( empty( $mapping['flags']['AddablePK'] ) ) {
                    $isSerial = true;
                    $pk       = reset( $keys );
                }
            }

            $fields = array();
            $values = array();
            foreach ( $mapping['fields'] as $field => $data ) {
                if ( !self::CheckAddable($data) ) {
                    continue;
                }

                if ( !empty( $data['key'] ) && $isSerial ) {
                    continue;
                }

                if ( $field == self::BranchId
                    && !empty( $mapping['flags']['CanRevisions'] )
                    && empty( $object->$field ) )
                {
                    continue;
                }

                $fields[] = $conn->Quote( $field );
                $values[] = '@' . $field;
            }

            // prepare sql query
            $query = 'INSERT INTO ' . $conn->quote( $mapping["table"] ) . ' ( ' . implode( ', ', $fields ) . ' ) '
                    . ' VALUES ( ' . implode( ', ', $values ) . ' ) ';
            ;

            // WithReturning Keys
            if ( !empty( $options )
                 && !empty( $options[BaseFactory::WithReturningKeys] )
                 && $isSerial )
            {
                switch( $conn->GetClassName() ) {
                    case 'PgSqlConnection':
                        $query .= 'RETURNING ( ' . $conn->Quote( $pk ) . ' ) ';
                        break;
                }
            }

            return $query . ';' ;
        }


        /**
         * Prepare Update statement string
         *
         * @param array  $mapping
         * @param IConnection $conn
         *
         * @return string
         */
        public static function PrepareUpdateStatement( array $mapping, IConnection $conn  ) {
            /// prepare sql query
            $begin  = 'UPDATE ' . $conn->quote( $mapping['table'] ) . ' SET ';
            $middle = '  ';
            $end    = ' WHERE ';

            $forUpdate = 0;
            $firstKey  = null;
            foreach ( $mapping['fields'] as $field => $data ) {
                if ( !self::checkUpdatable( $data ) ) {
                    continue;
                }

                if ( !empty( $data["key"] ) ) {
                    $end    .= $conn->quote( $field ) . " = @" . $field . " AND ";
                    if ( empty( $firstKey ) ) {
                    	$firstKey  = $conn->quote( $field ) . " = @" . $field;
                    }
                } else {
                    $middle .= $conn->quote( $field ) . " = @" . $field . ",";
                    $forUpdate ++;
                }
            }

            if ( !empty( $firstKey ) && $forUpdate == 0 )  {
            	$middle = $firstKey;
            }

            $middle = rtrim( $middle, ',' );
            $end = rtrim( $end, 'AND  ');

            return $begin . $middle . $end;
        }


        /**
         * Prepare Update By Mask Statement
         *
         * @param array $searchArray
         * @param array $changes
         * @param array $mapping
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareUpdateByMaskStatement( &$searchArray, $changes, array $mapping, IConnection $conn  ) {
            /// prepare sql query
            $begin  = 'UPDATE ' . $conn->quote( $mapping['table'] ) . ' SET ';
            $middle = '  ';

            foreach ( $mapping["fields"] as $field => $data ) {
                if ( !self::checkUpdatable( $data ) ) {
                    continue;
                }

                if ( !in_array( $field, $changes ) ) {
                    continue;
                }

                if ( empty( $data['key'] ) ) {
                    $middle .= $conn->quote( $field ) . ' = @update_' . $field . ',';
                }
            }

            $middle = rtrim( $middle, ',' );
            $end    = self::PrepareGetOrCountFields( $searchArray, $mapping, array(BaseFactory::WithoutDisabled=>false), $conn );

            return $begin . $middle . $end;
        }


        /**
         * @static
         * @param  array $searchArray
         * @param  array $mapping
         * @param  array $options
         * @param  IConnection $conn
         * @param string $prefix
         * @return string
         */
        public static function PrepareGetOrCountFields( $searchArray, array $mapping, $options, $conn, $prefix = ""  ) {
            if ( ! empty( $prefix ) ) {
                $prefix .= '.';
            }

            $query       = ' WHERE TRUE ';
            $hasStatusId = false;
            foreach ( $searchArray as $field => $value ) {
                if ( $field == self::StatusId ) {
                    $hasStatusId = true;
                }

                if ( is_null( $value ) || $field == self::Page  || $field == self::PageSize ) {
                    continue;
                }

                $complexType = null;
                if ( isset( $mapping['search'][$field] )  ) {
                    $dbField  = $mapping['search'][$field]['name'];
                    $type     = $mapping['search'][$field]['type'];
                    $operator =  ( !empty( $mapping['search'][$field]['searchType'] ) ) ? $mapping['search'][$field]['searchType'] : SEARCHTYPE_EQUALS;
                } elseif ( isset( $mapping['fields'][$field] ) ) {
                    $dbField  = $field;
                    $type     = $mapping['fields'][$field]['type'];
                    $operator =  ( !empty( $mapping['fields'][$field]['searchType'] ) ) ? $mapping['fields'][$field]['searchType'] : SEARCHTYPE_EQUALS;
                } else {
                    continue;
                }

                if ( isset( $dbField ) ) {
                    if ( ( $operator === SEARCHTYPE_ARRAY  || $operator === SEARCHTYPE_NOT_INARRAY )
                            && empty( $value ) ) {
                        continue;
                    }

                    $query .=  ' AND ' . BaseFactory::GetOperatorString( $operator, $prefix . $conn->quote( $dbField ),   '@' . $field, $type, $complexType, $conn );
                }
            }

            if ( !isset($options[BaseFactory::WithoutDisabled]) ) {
                $options[BaseFactory::WithoutDisabled] = true;
            }

            if ( !empty( $options[BaseFactory::WithoutDisabled] ) && $hasStatusId  ) {
                $query .=  ' AND ' . BaseFactory::GetOperatorString( SEARCHTYPE_NOT_EQUALS,  $prefix . $conn->quote( self::StatusId ),   "@eaze_" . BaseFactory::WithoutDisabled, TYPE_INTEGER, null, $conn );
            }

            if ( !empty( $options[BaseFactory::CustomSql] ) ) {
                $query .= $options[BaseFactory::CustomSql];
            }

            return $query;
        }


        /**
         * Prepare Count string
         *
         * @param array       $searchArray
         * @param array       $mapping
         * @param array       $options
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareCountString( &$searchArray, array $mapping, $options, IConnection $conn  ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $options, $conn->GetName() );
            $query       = 'SELECT count(*) as ' . $conn->quote( "count" ) . ' FROM  ' . $conn->quote( $mapping["view"] ). ' t';
            $query      .= self::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );

            return $query;
        }


        /**
         * Prepare Get String
         *
         * @param array $searchArray
         * @param array $mapping
         * @param $options
         * @param IConnection $conn
         * @return string
         */
        public static function PrepareGetString( &$searchArray, array $mapping,  &$options, IConnection $conn  ) {
            $searchArray = BaseFactory::ValidateSearch( $searchArray, $mapping, $options, $conn->GetName() );
            $query       = 'SELECT ' . ((!empty($options[BaseFactory::WithColumns])) ?  $options[BaseFactory::WithColumns] : "*" )
                            . '  FROM  ' . $conn->quote( $mapping["view"] ) . ' t';
            $query      .= self::PrepareGetOrCountFields( $searchArray, $mapping, $options, $conn );
            $query      .= self::GetOrderByString( $options, $conn );

            if ( BaseFactory::CanPages( $mapping, $options ) ) {
                $query .=  " LIMIT @pageSize OFFSET @pageOffset ";
            }

            return $query;
        }


        /**
         * Prepare Get String
         *
         * @param array $mapping
         * @param IConnection $conn
         *
         * @return string
         */
        public static function PrepareGetCurrentIdString( array $mapping, IConnection $conn  ) {
            $key = null;

            foreach ( $mapping['fields'] as $field => $data ) {
                if ( !empty( $data['key'] ) ) {
                    $key = $field;
                    break;
                }
            }

            $query = 'SELECT max( ' . $conn->quote( $key ) . ' ) as '
                     . $conn->quote( 'key' )
                     . ' FROM  ' . $conn->quote( $mapping['table'] )
            ;

            return $query;
        }


        /**
         * Prepares string for physical removing object instance from a table.
         *
         * @param array $mapping           Object mapping.
         * @param IConnection $connection  Database connection.
         * @return string
         */
        public static function PrepareDeleteString( array $mapping, $connection) {
            $table  = $connection->quote( $mapping["table"] );
            $keys   = BaseFactoryPrepare::GetPrimaryKeys( $mapping );

            $clause = "";
            foreach ( $keys as $key ) {
                $clause .= " " . $connection->quote( $key ) . ' = @' . $key . ' AND ';
            }

            if ( !empty($keys) ) {
                $result =  'DELETE FROM ' . $table . ' WHERE' . $clause . ' TRUE;';
            } else {
                $result = "SELECT FALSE";
                Logger::Error(  "%s doesn't have primary keys", $table );
            }

            return $result;
        }


        /**
         * Prepare Delete by mask statement
         *
         * @param $searchArray
         * @param array $mapping     Object mapping
         * @param IConnection $conn  Database connection
         * @return string
         */
        public static function PrepareDeleteByMaskStatement( $searchArray, array $mapping, $conn) {
            $table   = $conn->quote( $mapping['table'] );
            $result  =  'DELETE FROM ' . $table . " ";
            $result .= self::PrepareGetOrCountFields( $searchArray, $mapping, array( BaseFactory::WithoutDisabled => false ), $conn );

            $emptySearch = true;
            foreach ( $searchArray as $value ) {
                if ( !empty( $value ) ) {
                    $emptySearch = false;
                    break;
                }
            }

            if ( $emptySearch ) {
                $result = 'SELECT FALSE;';
                Logger::Warning( '%s has no search parameters', $mapping['class'] );
            }

            return $result;
        }


        /**
         * Get Primary Keys
         *
         * @param array $mapping
         * @return array
         */
        public static function GetPrimaryKeys( array $mapping ) {
            $result = array();

            foreach ( $mapping['fields'] as $field => $data ) {
                if ( !empty( $data['key'] ) ) {
                    $result[] = $field;
                }
            }

            return $result;
        }


        /**
         * Get Order By Body String
         *
         * @param array $options
         * @param IConnection $conn
         * @return string
         */
        public static function GetOrderByString( &$options, IConnection $conn ) {
            if ( empty($options[BaseFactory::OrderBy]) ) {
                return null;
            }

            /**
            // Order By Array Example or SQL STRING
            $options["orderBy"] = array(
                array(
                    "name"   => "newsId"
                    , "sort" => "ASC" // optional, default is ASC
                ), array(
                    "name"   => "newsId"
                    , "sort" => "DESC"
                )
            );
            */

            if ( is_array( $options[BaseFactory::OrderBy] ) ) {
                $orderByString = 'ORDER BY ';
                foreach ( $options['orderBy'] as $condition ) {
                    $orderByString .= $conn->quote(  $condition["name"] ) . " " . ( !empty( $condition['sort'] )  ?  $condition['sort'] : 'ASC' ) . ', ';
                }
                $orderByString = rtrim( $orderByString, ", " );

                $options[BaseFactory::OrderBy] = $orderByString;
            } else {
                if ( strpos( strtolower($options[BaseFactory::OrderBy]), 'order by' ) === false ) {
                    $options[BaseFactory::OrderBy] = 'ORDER BY ' . $options[BaseFactory::OrderBy];
                }
            }

            return ' ' . $options[BaseFactory::OrderBy];
        }


        /**
         * Glue Lists
         *
         * @param array  $source        source array
         * @param array  $append        array for append
         * @param string $keyId         key in source array
         * @param string $destination   destination array in source
         */
        public static function Glue( &$source, $append, $keyId, $destination ) {
            foreach ( $append as $key => $value ) {
                $srcKeyId = $value->$keyId;
                if ( !empty( $source[$srcKeyId] ) ) {
                    $srcObjectArray = $source[$srcKeyId]->$destination;
                    $srcObjectArray[$key] = $value;
                    $source[$srcKeyId]->$destination = $srcObjectArray;
                }
            }
        }


        /**
         * Collapse source objects
         *
         * @see ArrayHelper::Collapse();
         * @param array  $sourceObjects  the array of source objects
         * @param string $collapseKey    the object field
         * @param bool   $toArray        the collapse mode
         * @return array
         */
        public static function Collapse( $sourceObjects, $collapseKey, $toArray = true  ) {
            return ArrayHelper::Collapse( $sourceObjects, $collapseKey, $toArray );
        }


        /**
         * Get Cache Tags By Mapping
         * @param $mapping
         * @return
         */
        public static function GetCacheTags( $mapping ) {
        	if ( empty( self::$tagsCache[$mapping['class']] ) ) {
        		self::$tagsCache[$mapping['class']] = array_merge( array( $mapping['table']), $mapping['cacheDeps'] );
        	}
        	
        	return self::$tagsCache[$mapping['class']];
        }


        /**
         * Save Cache Result
         * @param $cacheKey
         * @param $tags
         * @param $result
         * @param int $expires
         * @param null $timestamp
         * @return bool
         */
        public static function SaveCacheResult( $cacheKey, $tags, $result, $expires = 3600, $timestamp = null ) {
            $cacheResult = array(
                'time'    => !empty( $timestamp ) ? $timestamp : time()
            	, 'tags'  => $tags
                , 'data'  => $result
            );
            
            if ( empty( $expires ) ) {
                $expires = 3600;
            }
            $expires = $expires * 2;
            return MemcacheHelper::Set( $cacheKey, $cacheResult, MEMCACHE_COMPRESSED, $expires );
        }


        /**
         * StrLen method used for BaseFactory
         * If mb_strlen exists use it instead of strlen
         * @param string $str
         * @return string lower string
         */
        public static function StrLen( $str ) {
            static $usemb;

            if ( is_null( $usemb ) ) {
                $usemb = function_exists( 'mb_strlen' );
            }

            if ( $usemb === true ) {
                return mb_strlen( $str );
            }

            return strlen( $str );
        }
    }
?>