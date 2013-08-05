<?php
    /**
     * Db Type Json Array
     * @package Eaze
     * @subpackage Database
     */
    class DbTypeJsonArray implements IComplexType {

        /**
         * @var ISqlConvert
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
         * @param mixed|null $value
         * @return string
         */
        public function ToDatabase( $value = null ) {
            return $this->converter->ToString( json_encode( $value ) );
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
            if ( empty( $value ) || is_array( $value ) ) {
                return $errors;
            }

            $errors['format'] = 'format';

            return $errors;
        }


        /**
         * Get PHP value from Database
         * @param  string $parameter
         * @return array|mixed
         */
        public static function FromDatabase( $parameter ) {
            return json_decode( $parameter, true );
        }


        /**
         * Get PHP value from Request
         * @param array|mixed $value
         * @return array|mixed
         */
        public static function FromRequest( $value ) {
            return Convert::ToArray( $value );
        }


        /**
         * Get Complex Type Name
         * @return string
         */
        public static function GetName() {
            return 'json';
        }
    }
?>