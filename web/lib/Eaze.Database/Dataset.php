<?php
    /**
     * Helps to manage sets of database data resources.
     *
     * @package Eaze
     * @subpackage Database
     * @author  max.05
     */
    abstract class DataSet {

        /**
         * DataSet Columns
         *
         * @var array
         */
        public $Columns = array();        
        
        /**
         * The cursor.
         * 
         * @var integer
         */
        protected $cursor  = -1;
        
        /**
         * Database data resource.
         *
         * @var resource
         */
        protected $resource = null;
        
        /**
         * Represents database data as array of the rows.
         * 
         * @var array
         */
        protected $data = array();
        
        /**
         * The number of the rows in the result resource.
         * 
         * @var integer
         */
        protected $size = 0;

        /**
         * Sql Connection
         *
         * @var IConnection
         */
        protected $connection;
        

        /**
         * Sets the cursor to a first element.
         */
        public function First() {
            $this->cursor = 0;
        }
        
        
        /**
         * Sets the cursor to a last element.
         */
        public function Last() {
            $this->cursor = $this->size - 1;
        }


        /**
         * @param IConnection $connection
         */
        public function __construct( IConnection $connection ) {
            $this->connection = $connection;
        }


        /**
         * Sets the cursor to a next element.
         *
         * @return boolean  <code>true</code> if cursor moved to the next element, otherwise <code>false</code>
         */
        public function Next() {
            if ( isset( $this->data[$this->cursor] ) ) {
                unset( $this->data[$this->cursor] );
            }

            $this->cursor ++;
            
            if ( $this->cursor >= $this->size ) {
                $this->cursor --;
                return false;
            }
            
            return true;
        }
        
        
        /**
         * Sets the cursor to a previous element.
         *
         * @return boolean  <code>true</code> if cursor moved to the previous element, otherwise <code>false</code>
         */
        public function Previous() {
            $this->cursor--;
            
            if ( $this->cursor > -1 ) {
                return true;
            }
            
            $this->cursor++;
            return false;
        }
        
        
        /**
         * Sets the cursor to the initial value.
         */
        public function Reset() {
            $this->cursor = -1;   
        }
        
        
        /**
         * Returns the size of the set.
         *
         * @return integer  The number of elements in the data source
         */
        public function GetSize() {
            return ( $this->size );
        }    
        
        
        /**
         * Sets the cursor position.
         *
         * @param integer $position  the cursor position
         */
        public function SetCursor( /*integer*/ $position ) {
            if ( ($position > -1) && ($position < $this->size) ) {
                $this->cursor = $position;
            }
        }
        
        
        /**
         * Returns the current position of the cursor.
         *
         * @return integer The cursor value
         */
        public function GetCursor() {
            return ( $this->cursor );
        }
        
       
        /**
         * Clears the DataSet data.
         */
        public function Clear() {
            $this->resource = null;
            $this->data     = null;    
            $this->size     = 0;
            $this->cursor   = -1;
        }

        /**
         * Get Integer
         * @abstract
         * @param  string $name column name
         * @return integer
         */
        abstract function GetInteger( $name );

        /**
         * Get String
         * @abstract
         * @param  string $name column name
         * @return string
         */
        abstract function GetString( $name );

        /**
         * Get Float
         * @abstract
         * @param  string $name column name
         * @return float
         */
        abstract function GetFloat( $name );

        /**
         * Get Double
         * @abstract
         * @param  string $name column name
         * @return float
         */
        abstract function GetDouble( $name );

        /**
         * Get Boolean
         * @abstract
         * @param  string $name column name
         * @return bool
         */
        abstract function GetBoolean( $name );

        /**
         * Get Date Time
         * @abstract
         * @param  string $name column name
         * @return DateTimeWrapper
         */
        abstract function GetDateTime( $name );


        /**
         * Get Value
         * @abstract
         * @param string $name  column name
         * @param string $type
         * @return mixed
         */
        abstract function GetValue( $name, $type = TYPE_STRING );


        /**
         * Get Unprocessed Value
         * @abstract
         * @param  string $name
         * @return mixed
         */
        abstract function GetParameter( $name );


        /**
         * Get ComplexType Value
         * @abstract
         * @param  string $name
         * @param  string $alias complexType Alias
         * @return array|mixed
         */
        public function GetComplexType( $name, $alias ) {
            $result = null;
            $type = $this->connection->GetComplexType( $alias );
            if ( $type !== null ) {
                $result = $type->FromDatabase( $this->GetParameter( $name ) );
            }

            return $result;
        }


        /**
         * Get Affected Rows
         * @return int
         */
        abstract function GetAffectedRows();
    }
?>
