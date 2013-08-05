<?php
    /**
     * Database connection for PostgreSQL
     *
     * @package Eaze
     * @subpackage PgSql
     * @author max3.05, sergeyfast
     */
    class PgSqlConnection implements IConnection {

        /**
         * Complex Type Mapping
         * @var array
         */
        public static $ComplexTypeMapping = array(
            'php'        => 'DbTypePhpArray'
            , 'json'     => 'DbTypeJsonArray'
            , 'int[]'    => 'PgSqlTypeIntArray'
            , 'float[]'  => 'PgSqlTypeFloatArray'
            , 'string[]' => 'PgSqlTypeStringArray'
            , 'hstore'   => 'PgSqlTypeHStoreArray'
            , 'point'    => 'PgSqlTypePoint'
        );


        /**
         * Array of Complex Types
         * @var IComplexType[]
         */
        private static $complexTypes = array();


        /**
         * PostgreSQL database server host
         *
         * @var string
         */
        private $host = 'localhost';

        /**
         * PostgreSQL database server port
         *
         * @var int
         */
        private $port = 5432;

        /**
         * PostgreSQL database user
         *
         * @var string
         */
        private $user = 'postrges';

        /**
         * PostgreSQL database user password
         *
         * @var string
         */
        private $password = '';

        /**
         * PostgreSQL database name
         *
         * @var string
         */
        private $dbname = 'postrges';

        /**
         * Connection charset
         *
         * @var string
         */
        private $charset = null;

        /**
         * Connection instance resource
         *
         * @var resource
         */
        private $connection;

        /**
         * Use pconnect instead of connect
         * 
         * @var boolean
         */        
        private $isPersistent = false;        
        
        /**
         * Converter for PostgreSQL values
         *
         * @var PgSqlConvert
         */
        private $converter;

        /**
         * Eaze Connection Name
         * @var string
         */
        private $name;


        /**
         * Form connection string from the connection parameters.
         *
         * @return string Connection string for the connection.
         */
        public function GetConnectionString() {
            $connectionString = sprintf( 'host=%s port=%s dbname=%s user=%s password=%s', $this->host, $this->port, $this->dbname, $this->user, $this->password );

            return $connectionString;
        }


        /**
         * Executes specified query and return result DataSet.
         *
         * @param string $query  Sql query to execute.
         * @return PgSqlDataSet Result DataSet.
         */
        public function ExecuteQuery( $query ) {
            if ( !is_resource( $this->connection ) ) {
                $this->open();
            }

            /** @var resource $resource  */
            $resource = pg_exec( $this->connection, $query );

            return new PgSqlDataset( $resource, $this );
        }


        /**
         * Execute Sql query and return result statement/
         *
         * @param string $query Sql query to execute.
         * @return boolean Return <code>true</code> if command executed successfully, otherwise return <code>false</code>.
         */
        public function ExecuteNonQuery( $query ) {
            if ( !is_resource( $this->connection ) ) {
                $this->open();
            }

            $resource = pg_query( $this->connection, $query );

            return !empty( $resource );
        }


        /**
         * Checks if current connection instance is opened.
         *
         * @return boolean  <code>True</code> if connection is opened, otherwise <code>false</code>.
         */
        public function IsOpened() {
            return is_resource( $this->connection );
        }


        /**
         * Starts transaction.
         * @return bool
         */
        public function Begin() {
            return $this->executeNonQuery( 'BEGIN TRANSACTION' );
        }


        /**
         * Commits current transaction.
         * @return bool
         */
        public function Commit() {
            return $this->executeNonQuery( 'COMMIT TRANSACTION' );
        }


        /**
         * Rollbacks current transaction.
         * @return bool
         */
        public function Rollback() {
            return $this->executeNonQuery( 'ROLLBACK TRANSACTION' );
        }


        /**
         * Determines if transaction started.
         *
         * @return bool Return <code>true</code> if current connection is in transaction, otherwise <code>false</code>
         */
        public function IsTransaction() {
            return ( PGSQL_TRANSACTION_IDLE != pg_transaction_status( $this->connection ) );
        }


        /**
         * Quote String
         * like prepare
         *
         * @param string $str
         * @return string
         */
        public function Quote( $str ) {
            return $this->converter->Quote( $str );
        }


        /**
         * Gets last error message string of the connection.
         *
         * @return string Last message error string if the connection.
         */
        public function GetLastError() {
            if ( is_resource( $this->connection ) ) {
                return pg_last_error( $this->connection );
            }

            return 'Connection was not opened yet';
        }

        /**
         * Opens connection using specified parameters
         * @return bool
         */
        public function Open() {
            if ( is_resource( $this->connection ) ) {
                return true;
            }

            $connectionString = $this->getConnectionString();

            if ( !empty( $this->isPersistent ) ) {
                $this->connection = pg_pconnect( $connectionString );
            } else {
                $this->connection = pg_connect( $connectionString );
            }

            if ( !empty( $this->charset ) ) {
                $result = $this->executeNonQuery( 'SET CLIENT_ENCODING TO ' . $this->charset );

                if ( !$result ) {
                    Logger::Warning(  'Charset %s was not found. Previous charset kept', $this->charset );
                }
            }

            return is_resource( $this->connection );
        }


        /**
         * Close current connection
         * @return bool
         */
        public function Close() {
            if ( is_resource( $this->connection ) ) {
                return pg_close( $this->connection );
            }

            return false;
        }


        /**
         * Get SqlConverter
         * @return PgSqlConvert
         */
        public function GetConverter() {
            return $this->converter;
        }


        /**
         * Initializes PgSqlConnection instance
         *
         * @param string $host      Database server host
         * @param string $port      Database server port
         * @param string $dbname    Database name
         * @param string $user      Database user
         * @param string $password  Database user password
         * @param string $charset
         * @param bool   $isPersistent Use pconnect instead of connect
         * @param string $name eaze connection name
         * @return PgSqlConnection
         *
         */
        public function __construct( $host       = null
                                     , $port     = null
                                     , $dbname   = null
                                     , $user     = null
                                     , $password = null
                                     , $charset  = null
                                     , $isPersistent = false
                                     , $name         = null ) {
            $this->host         = !empty( $host )     ? $host     : 'localhost';
            $this->port         = !empty( $port )     ? $port     : 5432;
            $this->dbname       = !empty( $dbname )   ? $dbname   : 'postgres';
            $this->user         = !empty( $user )     ? $user     : 'postgres';
            $this->password     = !empty( $password ) ? $password : '';
            $this->charset      = !empty( $charset )  ? $charset  : '';
            $this->isPersistent = $isPersistent;
            $this->name         = $name;
            $this->converter    = new PgSqlConvert( $this );
        }


        /**
         * Get Complex Type
         * @param  string $alias  (e.g. php, json, int[], string[], hstore)
         * @return IComplexType
         */
        public function GetComplexType( $alias  ) {
            if ( empty( self::$ComplexTypeMapping[$alias] ) ) {
                return null;
            }

            if ( empty( self::$complexTypes[$alias] ) ) {
                self::$complexTypes[$alias] = new self::$ComplexTypeMapping[$alias]( $this->converter );
            }

            return self::$complexTypes[$alias];
        }


        /**
         * Get Connection Resource
         * @return resource
         */
        public function GetResource() {
            return $this->connection;
        }


        /**
         * Get Connection Name
         * @return string
         */
        public function GetName() {
            return $this->name;
        }


        /**
         * Returns ClassName
         * @return string
         */
        public function GetClassName() {
            return __CLASS__;
        }


        /**
         * Get Last Insert Id (if applicable)
         * @return integer
         */
        public function GetLastInsertId() {
            return false;
        }

    }
?>