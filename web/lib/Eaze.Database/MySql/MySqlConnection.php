<?php
    /**
     * Database connection for MySQL
     *
     * @package Eaze
     * @subpackage MySql
     * @author max3.05, sergeyfast
     */
    class MySqlConnection implements IConnection {

        /**
         * Complex Type Mapping
         * @var array
         */
        public static $ComplexTypeMapping = array(
            'php'    => 'DbTypePhpArray'
            , 'json' => 'DbTypeJsonArray'
        );

        /**
         * Array of Complex Types
         * @var IComplexType[]
         */
        private static $complexTypes = array();

        /**
         * MySQL database server host
         *
         * @var string
         */
        private $host = 'localhost';

        /**
         * MySQL database server port
         *
         * @var string
         */
        private $port = '3306';

        /**
         * MySQL database user
         *
         * @var string
         */
        private $user = 'root';

        /**
         * MySQL database user password
         *
         * @var string
         */
        private $password = '';

        /**
         * MySQL database name
         *
         * @var string
         */
        private $dbname = 'mysql';

        /**
         * Connection charset
         *
         * @var string
         */
        private $charset = 'UTF8';

        /**
         * Use pconnect instead of connect
         *
         * @var boolean
         */
        private $isPersistent = false;

        /**
         * Connection instance resource
         *
         * @var resource
         */
        private $connection;

        /**
         * Converter for MySQL values
         *
         * @var MySqlConvert
         */
        private $converter;

        /**
         * Is in Transaction
         *
         * @var bool
         */
        private $isTransaction = false;

        /**
         * Eaze Connection Name
         * @var string
         */
        private $name;


        /**
         * Executes specified query and return result DataSet
         *
         * @param string $query  Sql query to execute.
         * @return MySqlDataSet Result DataSet.
         */
        public function ExecuteQuery( $query ) {

            if ( !is_resource( $this->connection ) ) {
                $this->open();
            }

            $resource = mysql_query( $query, $this->connection );
            if ( $resource === false ) {
                Logger::Error( $this->getLastError() );
            }

            return new MySqlDataset( $resource, $this );
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

            if ( ! is_resource( $this->connection ) ) {
                return false;
            }

            $resource = mysql_query( $query, $this->connection );
            return !empty( $resource );
        }


        /**
         * Starts transaction.
         */
        public function Begin() {
            $this->isTransaction = $this->executeNonQuery( 'BEGIN' );

            return $this->isTransaction;
        }


        /**
         * Commits current transaction
         */
        public function Commit() {
            $this->ExecuteNonQuery( 'COMMIT' );
            $this->isTransaction = false;
        }


        /**
         * Rollbacks current transaction
         */
        public function Rollback() {
            if ( $this->isTransaction ) {
                $this->ExecuteNonQuery( 'ROLLBACK' );
                $this->isTransaction = false;
                return true;
            }

            return false;
        }


        /**
         * Determines if transaction started.
         *
         * @return bool Return <code>true</code> if current connection is in transaction, otherwise <code>false</code>
         */
        public function IsTransaction() {
            return $this->isTransaction;
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
         * Checks if current connection instance is opened.
         *
         * @return boolean  <code>True</code> if connection is opened, otherwise <code>false</code>.
         */
        public function IsOpened() {
            return is_resource( $this->connection );
        }

        /**
         * Gets last error message string of the connection.
         *
         * @return string Last message error string if the connection.
         */
        public function GetLastError() {
            if ( is_resource( $this->connection ) ) {
                return mysql_error( $this->connection );
            }

            return 'Connection was not opened yet';
        }


        /**
         * Opens connection using specified parameters
         *
         * @return boolean <code>True</code> if the connection was opened successfully, otherwise <code>false</code>.
         */
        public function Open() {
            if (!empty( $this->isPersistent ) ) {
                $this->connection = mysql_pconnect( $this->host . ':' . $this->port, $this->user, $this->password );
            } else {
                $this->connection = mysql_connect( $this->host . ':' . $this->port, $this->user, $this->password );
            }

            if ( !($this->connection) ) {
                return false;
            }

            $result = true;
            if ( !empty( $this->dbname ) ) {
                $result = mysql_select_db( $this->dbname, $this->connection );
            }
            if ( false === $result ) {
                mysql_close( $this->connection );
                $this->connection = null;
                return false;
            }

            if ( ! empty( $this->charset ) ) {
                $result = $this->executeNonQuery( 'SET NAMES ' . $this->converter->ToString( $this->charset ) );
                if ( ! $result ) {
                    Logger::Warning( 'Charset %s was not found. Previous charset kept', $this->charset );
                }
            }

            return true;
        }


        /**
         * Close current connection
         */
        public function Close() {
            $result = false;
            if ( is_resource( $this->connection ) ) {
                $result = mysql_close( $this->connection );
                $this->connection = null;
            }

            return $result;
        }


        /**
         * Initializes MySqlConnection instance
         *
         * @param string $host      Database server host
         * @param string $port      Database server port
         * @param string $dbname    Database name
         * @param string $user      Database user
         * @param string $password  Database user password
         * @param null $charset
         * @param bool $isPersistent
         * @param string $name
         * @return MySqlConnection
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
             $this->host         = $host;
             $this->port         = $port;
             $this->dbname       = $dbname;
             $this->user         = $user;
             $this->password     = $password;
             $this->isPersistent = $isPersistent;
             $this->name         = $name;

             if ( !empty( $charset ) ) {
                $this->charset  = $charset;
             }

             $this->converter = new MySqlConvert( $this );
        }


        /**
         * Get Complex Type
         * @param  string $alias  (e.g. php, json, int[], string[], hstore)
         * @return IComplexType
         */
        public function GetComplexType( $alias ) {
            if ( empty( self::$ComplexTypeMapping[$alias] ) ) {
                return null;
            }

            if ( empty( self::$complexTypes[$alias] ) ) {
                self::$complexTypes[$alias] = new self::$ComplexTypeMapping[$alias]( $this->converter );
            }

            return self::$complexTypes[$alias];
        }


        /**
         * Get SqlConverter
         * @return MySqlConvert
         */
        public function GetConverter() {
            return $this->converter;
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
            if ( is_resource( $this->connection ) ) {
                return mysql_insert_id( $this->connection );
            }

            return false;
        }

    }
?>