<?php
    /**
     * Object Helper
     * @package Eaze
     * @subpackage Helpers
     * @author sergeyfast
     */
    class ObjectHelper {

        /**
         * Data to JSON
         * @param mixed $object
         * @return string
         */
        public static function ToJSON( $object ) {
            if ( function_exists( 'json_encode' ) ) {
                return json_encode( $object );
            } else {
                $f = FirePHP::getInstance( true );
                return $f->json_encode( $object );
            }
        }


        /**
         * Data from JSON
         * @param string $string
         * @return mixed
         */
        public static function FromJSON( $string ) {
            return json_decode( $string );
        }
    }

?>