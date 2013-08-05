<?php

    /**
     * PgSql Type Point
     * @package Eaze
     * @subpackage Database
     * @subpackage PgSql
     * @author sergeyfast
     */
    class PgSqlTypePoint implements IComplexType {

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
            if ( $value === null || empty( $value ) ) {
                return 'null';
            }

            $result = '(' . implode( ',', array_map( array( 'PgSqlConvert', 'ToFloat' ), $value ) ) . ')';

            return $this->converter->ToString( $result );
        }


        /**
         * Validate PHP Value before Save to Database
         * @param array|mixed $value
         * @param array $structure
         * @param array|null $options
         * @return array errors array
         */
        public function Validate( $value, array $structure, $options = null ) {
            $errors = array();

            //format check
            if( $value !== null
                && ( !is_array( $value ) || !in_array( count( $value ), array( 0, 2 ) ) ) )
            {
                return array( 'format' => 'format' );
            }

            // if has value check for null
            if ( !empty( $value ) ) {
                $value = array_values( $value );
                if ( is_null( $value[0] ) || is_null( $value[1] ) ) {
                    $errors['null'] = 'null';
                }
            }

            //nullable check from mapping
            if( isset( $structure['nullable'] ) ) {
                switch ( $structure['nullable'] ) {
                    case 'CheckEmpty':
                    case 'No':
                        if( empty( $value ) ) {
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
            if ( empty( $parameter ) ) {
                return array();
            }

            $value  = trim( $parameter, '() ' );
            $result = ( strlen( $value ) == 0 ) ? array() : explode( ',', $value );
            $result = array_map( array( 'Convert', 'ToFloat' ), $result );

            return $result;
        }


        /**
         * Get PHP value from Request
         * @param array|mixed $value
         * @return array
         */
        public static function FromRequest( $value ) {
            $value = array_map( array( 'Convert', 'ToFloat' ), Convert::ToArray( $value ) );
            if ( $value !== null ) {
                if ( count( $value ) == 2 ) {
                    $value = array_values( $value );
                } else if ( count( $value ) >= 2 ) {
                    $value = array_slice( array_values( $value ), 0, 2 );
                } else if ( count( $value ) < 2 ) {
                    $value = null;
                }
            }

            // check for null values
            if  ( !is_null( $value ) && is_null( $value[0] ) && is_null( $value[1] ) ) {
                $value = null;
            }

            return $value;
        }


        /**
         * Get Complex Type Name
         * @return string
         */
        public static function GetName() {
            return 'point';
        }
    }
?>