<?php
    /**
     * BTSyncUtility
     * @package    EazyPhoto
     * @subpackage Albums
     * @author     Sergeyfast
     */
    class BTSyncUtility {

        /**
         * Params
         * @var string[]
         */
        private static $params = array();


        /**
         * Init Module
         *
         * @param DOMNodeList $params  the params node list
         * @static
         */
        public static function Init( DOMNodeList $params ) {
            foreach ( $params as $param ) {
                /** @var DOMElement $param */
                self::$params[$param->getAttribute( 'name' )] = $param->nodeValue;
            }

            if ( !isset( self::$params['host'] ) ) {
                self::$params['host'] = '127.0.0.1';
            }

            if ( !isset( self::$params['port'] ) ) {
                self::$params['port'] = '8888';
            }

            if ( !isset( self::$params['user'] ) ) {
                self::$params['user'] = 'admin';
            }

            if ( !isset( self::$params['password'] ) ) {
                self::$params['password'] = '';
            }
        }


        /**
         * Get Client
         * @return BTSyncClient
         */
        public static function GetClient() {
            return new BTSyncClient( sprintf( 'http://%s:%d', self::$params['host'], self::$params['port'] ), self::$params['user'], self::$params['password'] );
        }
    }

?>