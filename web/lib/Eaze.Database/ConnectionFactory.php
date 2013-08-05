<?php
    /**
     * Connection Factory
     * @package Eaze
     * @subpackage Database
     */
    class ConnectionFactory {

        /**
         * Default Connection Name
         */
        const DefaultConnection = 'default';

        /**
         * Connections
         * @var IConnection[]
         */
        private static $connections = array();

        /**
         * Connection params
         * @var array
         */
        private static $params = array(
            'driver'       => null
            , 'name'       => self::DefaultConnection
            , 'host'       => null
            , 'port'       => null
            , 'dbname'     => null
            , 'user'       => null
            , 'password'   => null
            , 'encoding'   => null
            , 'persistent' => false
        );

        
        /**
         * Add Connection config
         *
         * @param array $params
         * @return bool
         */
        public static function Add( $params ) {
            if ( empty( $params ) || empty( $params['driver'] ) ) {
                return false;
            }

            $params += self::$params;

            if ( !empty( $params['persistent'] ) ) {
                $params['persistent'] = Convert::ToBoolean( $params['persistent' ] );
            }

            if ( empty( $params['name'] ) ) {
                $params['name'] = self::$params['name'];
            }

            if ( isset( self::$connections[$params['name']] ) ){
                return false;
            }

            $className = $params['driver'] . 'Connection';
            
            ConnectionFactory::$connections[$params['name']] = new $className(
                $params['host']
                , $params['port']
                , $params['dbname']
                , $params['user']
                , $params['password']
                , $params['encoding']
                , $params['persistent']
            );
            
            return true;
        }
        
        
        /**
         * Get Opened Connection by Name
         * @param string $name [optional] connection name
         * @return IConnection
         */
        public static function Get( $name = self::DefaultConnection ) {
            if ( empty( $name ) ) {
                $name = self::DefaultConnection;
            }

            if ( !empty( self::$connections[$name] ) ) {
                return self::$connections[$name];
            }
            
            return null;
        }
        
        
        /**
         * Close connections and free resources
         */
        public static function Dispose() {
            foreach ( self::$connections as $connection ) {
                $connection->close();
            }
        }


        /**
         * Close and Remove connection from pool
         * @param string $name [optional] connection name
         * @return bool
         */
        public static function Remove( $name = self::DefaultConnection ) {
            if ( empty( $name ) ) {
                $name = self::DefaultConnection;
            }

            if ( !empty( self::$connections[$name] ) ) {
                self::$connections[$name]->Close();
                unset( self::$connections[$name] );
                return true;
            }

            return false;
        }


        /**
         * Begin Transaction
         * @static
         * @param string $name [optional] connection name
         * @return IConnection
         */
        public static function BeginTransaction( $name = self::DefaultConnection ) {
            $conn = ConnectionFactory::Get( $name );
            if ( !empty( $conn ) && !$conn->IsTransaction() ) {
                $conn->Begin();
            }

            return $conn;
        }

        /**
         * Commit or Rollback Transaction
         * @static
         * @param bool   $result  commit or rollback
         * @param string $name [optional] connection name
         * @return bool
         */
        public static function CommitTransaction( $result, $name = self::DefaultConnection ) {
            $conn = ConnectionFactory::Get( $name );
            if ( !empty( $conn ) && $conn->IsTransaction() ) {
                if ( $result ) {
                    $conn->Commit();
                } else {
                    $conn->Rollback();
                }

                return true;
            }

            return false;
        }
    }
?>