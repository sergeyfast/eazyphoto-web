<?php
    /**
     * PgSqlTypeHStoreArray
     *
     * @package Eaze
     * @subpackage Database
     * @subpackage PgSql
     * @author m.kabilov
     */
    class PgSqlTypeHStoreArray  implements IComplexType {

        const HstoreFormat = '"%s"=>"%s"';

        //const HstoreRegexp = '#(^|, )"(.*)"=>"(.*)"#siU';
        const HstoreRegexp = '#(^|, )"(.*?)"=>(?>"{(.*?)}"|"(.*?)")#si';

        private static $strSearch  = array( '"', '\'' );
        private static $strReplace = array( '\\\"', '\'\'' );

        /**
         * @var PgSqlConvert
         */
        private $converter;


        /**
         * @param ISqlConvert $converter
         */
        public function __construct( ISqlConvert $converter ) {
            $this->converter = $converter;
        }


        /**
         * @param  string $operator
         * @param string $field
         * @param  string $value
         * @return string
         */
        public function GetSearchOperatorString( $operator, $field, $value ) {
            switch( $operator ) {
                case SEARCHTYPE_EQUALS:
                    $result = sprintf( '%s = %s', $this->converter->Quote( $operator ), $this->ToDatabase( $value ) );
                    break;
                default:
                    Logger::Error( 'Invalid search type %s', $operator );
                    $result = 'false';
            }

            return $result;
        }


        /**
         * Save PHP value to Database
         * @param array $value
         * @return string
         */
        public function ToDatabase( $value = null ) {
            if ( $value === null ) {
                return 'null';
            }

            $hstoreArray = array();
            foreach ( $value as $k => $v ) {
                $k = str_replace( self::$strSearch, self::$strReplace, $k );
                $v = $v === null ? 'null' : str_replace( self::$strSearch, self::$strReplace, $v );
                
                $hstoreArray[] = sprintf( self::HstoreFormat, $k, $v );
            }

            $result = "'" . implode( ',', $hstoreArray ) . "'";

            return $result;
        }


        /**
         * Validate PHP Value before Save to Database
         * @param array|mixed $value
         * @param array $structure
         * @param array|null $options
         * @return array errors array
         */
        public function Validate( $value, array $structure, $options = null ) {
            // TODO: validate array values (must be either one-dimensional or scalar)
            $errors = array();

            //format check
            if( !is_array( $value ) && $value !== null ) {
                return array( 'format' => 'format' );
            }

            //nullable check
            if( isset( $structure['nullable'] ) ) {
                switch ( $structure['nullable'] ) {
                    case 'CheckEmpty':
                        if( !is_array( $value ) || empty( $value ) ) {
                            $errors['null'] = 'null';
                        }
                        break;
                    case 'No':
                        if( is_null( $value ) ) {
                            $errors['null'] = 'null';
                        }
                        break;
                }
            }

            return $errors;
        }


        /**
         * Get PHP value from Database
         * @param  string $parameter
         * @return array
         */
        public static function FromDatabase( $parameter ) {
            $result = array();
            if ( empty( $parameter ) ) {
                return $result;
            }

            if ( !preg_match_all( self::HstoreRegexp , $parameter, $params ) ) {
                return $result;
            }
            
            $params[2] = str_replace( '\"', '"', $params[2] );
            $params[3] = str_replace( '\"', '"', $params[3] );
            $params[4] = str_replace( '\"', '"', $params[4] );

            //merge with JSON parameters:
            foreach( array_filter($params[3]) as $key => $jsonParam ){
                if( isset( $params[4][$key] ) && empty( $params[4][$key] ) ){
                    $params[4][$key] = sprintf( '{%s}', $jsonParam );
                }
            }

            $result = array_combine( $params[2], $params[4] );

            return $result;
        }


        /**
         * Get PHP value from Request
         * @param array|mixed $value
         * @return array
         */
        public static function FromRequest( $value ) {
            $value = Convert::ToArray( $value );

            return $value;
        }


        /**
         * Get Complex Type Name
         * @return string
         */
        public static function GetName() {
            return 'hstore';
        }

    }
?>
