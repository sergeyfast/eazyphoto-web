<?php
    use Eaze\Core\Logger;

    /**
     * Daemon Utility
     */
    class DaemonUtility {

        /**
         * Format Parameters
         *
         * @param array $parameters
         * @return bool
         */
        private static function formatParameters( &$parameters ) {

            /**
             * Structure (value indicates required attribute)
             */
            $structure = [
                'package'            => true
                , 'method'           => true
                , 'title'            => true
                , 'maxExecutionTime' => false // default 00:03:00
                , 'startDate'        => false // default null
                , 'endDate'          => false // default null
                , 'active'           => false // default true
                , 'params'           => false // default null
            ];

            foreach ( $structure as $key => $value ) {
                if ( !isset( $parameters[$key] ) ) {
                    $parameters[$key] = null;
                }

                if ( is_null( $parameters[$key] ) && $value ) {
                    return false;
                }
            }

            return true;
        }


        /**
         * Initialize Daemon
         *
         * @param array $parameters
         * @return Daemon
         */
        public static function Init( $parameters ) {
            // check parameters value
            if ( empty( $parameters ) ) {
                Logger::Debug( "Parameters is null" );
                return null;
            }

            // check parameters structure
            $result = self::formatParameters( $parameters );
            if ( $result == false ) {
                Logger::Debug( "Parameters array is corrupted" );
                return null;
            }

            $daemon = Daemon::GetInstance( $parameters );
            if ( !$daemon ) {
                return false;
            }

            return $daemon;
        }


        /**
         * Run
         *
         * @param array $parameters
         * @return bool
         */
        public static function Run( $parameters ) {
            Logger::LogLevel( ELOG_DEBUG );

            $daemon = self::Init( $parameters );
            if ( !$daemon ) {
                Logger::Warning( 'Run failed' );
                return false;
            }

            return $daemon->Run();
        }
    }
