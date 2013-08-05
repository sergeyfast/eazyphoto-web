<?php
    /**
     * Database connection for MySQL via mysqli driver
     *
     * @package Eaze
     * @subpackage MySqli
     * @author sergeyfast
     */
    class MySqliConnection implements IConnection {

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
         * @var mysqli
         */
        private $connection;

        /**
         * Converter for MySQL values
         *
         * @var MySqliConvert
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
         * @var bool
         */
        private $isOpened = false;


        /**
         * Executes specified query and return result DataSet
         *
         * @param string $query  Sql query to execute.
         * @return MySqliDataSet Result DataSet.
         */
        public function ExecuteQuery( $query ) {
            if ( !$this->isOpened ) {
                $this->Open();
            }

            $result = $this->connection->query( $query );
            if ( $result === false ) {
                Logger::Error( $this->getLastError() );
            }

            return new MySqliDataset( $result, $this );
        }


        /**
         * Execute Sql query and return result statement/
         *
         * @param string $query Sql query to execute.
         * @return boolean Return <code>true</code> if command executed successfully, otherwise return <code>false</code>.
         */
        public function ExecuteNonQuery( $query ) {
            if ( !$this->isOpened ) {
                $this->Open();
            }

            $result = $this->connection->query( $query );
            if ( $result === false ) {
                Logger::Error( $this->getLastError() );
            }

            return true;
        }


        /**
         * Starts transaction
         * @return bool
         */
        public function Begin() {
            $result = false;
            if ( !$this->isTransaction ) {
                if ( !$this->isOpened ) {
                    $this->Open();
                }
                $result              = $this->connection->autocommit( false );
                $this->isTransaction = $result;
            }

            return $result;
        }


        /**
         * Commits current transaction
         * @return bool
         */
        public function Commit() {
            $result = false;
            if ( $this->isTransaction ) {
                $result              = $this->connection->commit();
                $this->isTransaction = false;
                $this->connection->autocommit( true );

            }

            return $result;
        }


        /**
         * Rollbacks current transaction
         */
        public function Rollback() {
            $result = false;
            if ( $this->isTransaction ) {
                $result              = $this->connection->rollback();
                $this->isTransaction = false;
                $this->connection->autocommit( false );
            }

            return $result;
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
            return $this->isOpened;
        }

        /**
         * Gets last error message string of the connection.
         *
         * @return string Last message error string if the connection.
         */
        public function GetLastError() {
            $result = $this->connection->error;
            if ( !$this->isOpened ) {
                $result = $this->connection->connect_error;
            }

            return $result;
        }


        /**
         * Hack for PHPUnit Support
         */
        function __wakeup() {
            $this->Open();
        }


        /**
         * Opens connection using specified parameters
         *
         * @return boolean <code>True</code> if the connection was opened successfully, otherwise <code>false</code>.
         */
        public function Open() {
            $this->connection = new mysqli( ($this->isPersistent ? 'p:' : '') . $this->host, $this->user, $this->password, $this->dbname, $this->port );

            if ( $this->connection->connect_errno ) {
                Logger::Warning( 'Failed to connect: %s', mysqli_connect_errno() );
                return false;
            }

            if ( $this->charset ) {
                if ( !$this->connection->set_charset( $this->charset ) ) {
                    Logger::Warning( 'Charset %s was not found. Previous charset %s kept', $this->charset, $this->connection->get_charset() );
                }
            }

            $this->isOpened = true;
            $this->connection->autocommit( true );

            return true;
        }


        /**
         * Close current connection
         */
        public function Close() {
            $result = false;
            if ( $this->isOpened ) {
                $this->Rollback();

                $result = $this->connection->close();
                $this->isOpened = false;
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
         * @param null   $charset
         * @param bool   $isPersistent
         * @param string $name
         * @return MySqliConnection
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

            $this->converter = new MySqliConvert( $this );
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
         * @return mysqli
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
            if ( $this->isOpened ) {
                return $this->connection->insert_id;
            }

            return false;
        }

    }
?>