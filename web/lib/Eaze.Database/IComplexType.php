<?php
    /**
     * IComplexType Interface for Database Support
     * @package Eaze
     * @subpackage Database
     * @author sergeyfast
     */
    interface IComplexType {

        /**
         * @abstract
         * @param ISqlConvert $converter
         */
        function __construct( ISqlConvert $converter );


        /**
         * Get Complex Type Name
         * @return string
         */
        static function GetName();


        /**
         * Get PHP value from Database
         * @param  string $parameter
         * @return array|mixed
         */
        static function FromDatabase( $parameter );


        /**
         * Get PHP value from Request
         * @param array|mixed $value
         * @return array|mixed
         */
        static function FromRequest( $value );


        /**
         * Save PHP value to Database
         * @param mixed|null $value
         * @return string
         */
        function ToDatabase( $value = null );


        /**
         * Validate PHP Value before Save to Database
         * @param array|mixed $value
         * @param array $structure     field structure from mapping
         * @param array|null $options  additional options
         * @return array errors array
         */
        function Validate( $value, array $structure, $options = null );


        /**
         * Returns expression
         * @abstract
         * @param  string $operator  search type operator (SEARCHTYPE_*)
         * @param  string $field     search field
         * @param  string $value     field value
         * @return string  '"title" = 1 ' or '`field` IN (1,2,3)'
         */
        function GetSearchOperatorString( $operator, $field, $value );
    }

?>