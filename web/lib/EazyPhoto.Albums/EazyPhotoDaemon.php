<?php
    /**
     * EazyPhotoDaemon
     * @package    EazyPhoto
     * @subpackage Albums
     * @author     Sergeyfast
     */
    class EazyPhotoDaemon implements IModule {

        /**
         * Update Album Meta url. ?id=<id>
         */
        const UpdateMeta = '/update/album-meta';

        /**
         * Queue Albums url
         */
        const UpdateAlbums = '/update/albums';

        /**
         * Params
         * @var string[]
         */
        private static $params = array();

        /**
         * Curl Options
         * @var int[]
         */
        public static $CurlOptions = array(
            CURLOPT_RETURNTRANSFER => 1
        );


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
                self::$params['port'] = '8889';
            }

        }


        /**
         * Send Request
         * @param int[] $options curl options
         * @return string
         */
        private static function sendRequest( $options ) {
            $ch = curl_init();
            curl_setopt_array( $ch, $options );
            $data = curl_exec( $ch );
            curl_close( $ch );

            return $data;
        }


        /**
         * Update Meta
         * @param int $albumId
         * @return bool
         */
        public static function UpdateMeta( $albumId ) {
            $options              = self::$CurlOptions;
            $options[CURLOPT_URL] = sprintf( '%s%s?id=%d', self::GetUrl(), self::UpdateMeta, $albumId );
            $data                 = self::sendRequest( $options );

            return !empty( $data );
        }


        /**
         * Queue Albums
         * @internal param int $albumId
         * @return bool
         */
        public static function QueueAlbums() {
            $options              = self::$CurlOptions;
            $options[CURLOPT_URL] = sprintf( '%s%s', self::GetUrl(), self::UpdateAlbums );
            $data                 = self::sendRequest( $options );

            return !empty( $data );
        }


        /**
         * Get Client
         * @return string
         */
        public static function GetUrl() {
            return sprintf( 'http://%s:%d', self::$params['host'], self::$params['port'] );
        }


        /**
         * Is Enabled. Default false
         * @return mixed
         */
        public static function Enabled() {
            return ArrayHelper::GetValue( self::$params, 'enabled', false );
        }
    }

?>