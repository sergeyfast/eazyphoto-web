<?php
    /**
     * Array Helper
     * @package Eaze
     * @subpackage Helpers
     */
    class ArrayHelper {

        /**
         * Merging arrays recursively
         * original array_merge_recursive converts all data to array and extends it if key already exists
         * so it may cause some bugs if array contains objects
         * this function does not convert values into arrays and replaces data in existing key
         *
         * @var array $array1
         * @var array $array2 [optional]
         * @var array $_ [optional]
         * @static
         * @return array
         */
        public static function MergeDistinct( array $array1, array $array2 = array(), array $_ = array() ) {
            $arrays = func_get_args();
            $merged = array_shift( $arrays );

            foreach( $arrays as $array ) {
                foreach ( $array as $key => &$value ) {
                    if ( is_array( $value ) && isset( $merged[$key] ) && is_array( $merged [$key] ) ) {
                        $merged[$key] = self::MergeDistinct( $merged[$key], $value );
                    } else {
                        $merged[$key] = $value;
                    }
                }
            }

           return $merged;
        }


        /**
         * Gets the first element in the array.
         *
         * @param array $param  Array to get first element.
         * @param boolean $key  Defines if needs to get element key. Default returns element value.
         * @return mixed
         */
        public static function GetFirstElement( array $param, $key = false ) {
            $value = reset( $param );
            if ( $key ) {
                return key( $param );
            }

            return $value;
        }


        /**
         * Sets value of the array with specified path.
         *
         * @param array $parent  Parent array to set value.
         * @param array $path    Path to the element.
         * @param mixed $value   Value of the element.
         * @param string $field
         * @return array
         */
        public static function SetValue( &$parent, array $path, $value, $field = null ) {
            if ( empty( $parent ) ) {
                $parent = array();
            }

            if ( empty( $path ) ) {
                return $parent;
            }

            if ( 1 == count( $path ) ) {
                $key = reset( $path );
                $parent[$key] = $value;

                return $parent;
            }

            $key = ArrayHelper::GetFirstElement( $path );
            unset( $path[ArrayHelper::GetFirstElement( $path, true )] );

            if ( ! isset( $parent[$key] ) ) {
                $parent[$key] = array();
            }

            if ( empty( $field ) ) {
                self::SetValue( $parent[$key], $path, $value );
            } else {
                self::SetValue( $parent[$key]->$field, $path, $value, $field );
            }
        }


        /**
         * Converts BaseTreeObject path to array.
         *
         * @param mixed $path   BaseTreeObject path.
         * @return array
         */
        public static function PathToArray( $path ) {
            $result = explode( ".", $path );

            return $result;
        }


        /**
         * Gets order number of the element with specified key.
         *
         * @param mixed $key       Key to find.
         * @param array $haystack  Array stack to search.
         * @return int position of key starting with 1
         */
        public static function GetOrderNumber( $key, $haystack ) {
            $position = array_search( $key, array_keys( $haystack ) );
            if ( $position !== false ){
                $position ++;
            }

            return $position;
        }


        /**
         * Collapse source objects
         *
         * @param array  $sourceObjects  the array of source objects
         * @param string $collapseKey    the object field
         * @param bool   $toArray        the collapse mode
         * @return array
         */
        public static function Collapse( $sourceObjects, $collapseKey, $toArray = true  ) {
            if ( empty( $sourceObjects ) ) {
                return null;
            }

            $result = array();
            foreach ( $sourceObjects as $object ) {
                if ( $toArray ) {
                    $result[$object->$collapseKey][] = $object;
                } else {
                    $result[$object->$collapseKey]   = $object;
                }
            }

            return $result;
        }


        /**
         * Get Array Value by Key or return default value
         * @static
         * @param  array      $array
         * @param  string|int $key   key for array_key_exists
         * @param mixed       $defaultValue optional
         * @return mixed
         */
        public static function GetValue( array $array, $key, $defaultValue = null ) {
            if ( array_key_exists( $key, $array ) ) {
                return $array[$key];
            }

            return $defaultValue;
        }

        /**
         * Get Array difference of two assoc arrays
         * result is array with 4 keys: 'identical', 'modified', 'added', 'missed'
         * 'identical' contains keys of equal values
         * 'modified' contains keys of modified values
         * 'added' contains keys of values presented only in $newArray
         * 'missed' contains keys of values presented only in $oldArray
         *
         * @static
         * @param array $newArray
         * @param array $oldArray
         * @param bool  $strict
         * @return array
         */
        public static function GetArrayDiff( $newArray, $oldArray, $strict = false ) {
            //result structure
            $result = array(
                'identical'     => array()
                , 'modified'    => array()
                , 'added'       => array()
                , 'missed'      => array()
            );

            //search missed, identical or modified elements in new array
            foreach( $oldArray as $key => $value ) {
                if( !array_key_exists( $key, $newArray ) ) {
                    $result['missed'][] = $key;
                } else {
                    $identical = ( $strict ? ( $value === $newArray[$key] ) : ( $value == $newArray[$key] ) );
                    if( $identical ) {
                        $result['identical'][] = $key;
                    } else {
                        $result['modified'][] = $key;
                    }
                }
            }

            //search added elements in old array
            foreach( $newArray as $key => $value ) {
                if( !array_key_exists( $key, $oldArray ) ) {
                    $result['added'][] = $key;
                }
            }

            return $result;
        }

        /**
         * Get objects field values
         *
         * @static
         * @param array  $objects  the array of source objects
         * @param array  $fields   main object field first, than sub objects fields if necessary
         *
         * @return array
         */
        public static function GetObjectsFieldValues( $objects, $fields ) {
            $result = array();

            if( !empty( $objects ) ) {
                foreach( $objects as $key => $object ) {
                    $value = $object;
                    
                    foreach( $fields as $field ) {
                        $value = $value->$field;
                    }

                    $result[$key] = $value;
                }
            }

            return $result;
        }

        /**
         * Get objects tree collapsed by self parent foreign key
         *
         * @static
         * @param array     $objects    the array of source objects
         * @param string    $primaryKey title of object primary key (ex. "categoryId")
         * @param string    $parentKey  title of object self parent foreign key (ex. "parentCategoryId")
         * @param string    $nodes      title of object array field with will contain child objects
         * @return array
         */
        public static function GetObjectsTree( $objects, $primaryKey, $parentKey, $nodes ) {
            $tree = array();

            foreach( $objects as $object ) {
                $id  = $object->$primaryKey;
                $pid = $object->$parentKey;
                if( is_null( $object->$nodes ) ) {
                    $object->$nodes = array();
                }

                if( empty( $pid ) ) {
                    $tree[$id] = $object;
                } else if ( isset( $objects[$pid] ) ) {
                    $objectNodes            = $objects[$pid]->$nodes;
                    $objectNodes[$id]       = $object;
                    $objects[$pid]->$nodes  = $objectNodes;
                }
            }

            return $tree;
        }
    }

?>