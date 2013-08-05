<?php
    /**
     * Helps to manage sets of MySQL database data resources.
     *
     * @package Eaze
     * @subpackage MySqli
     * @author  sergeyfast
     */
    class MySqliDataSet extends DataSet {

        /**
         * Initializing instance.
         *
         * @param mysqli_result $resource  mysql resource.
         * @param IConnection $connection
         * @return MySqliDataSet
         */
        public function __construct(  $resource, IConnection $connection ){
            parent::__construct( $connection );

            if ( $resource instanceof mysqli_result ) {
                $this->resource = $resource;
                $this->size     = $resource->num_rows;

                $fields = $resource->fetch_fields();
                foreach( $fields as $field ) {
                    $name = $field->name;
                    $this->Columns[$name] = $name;
                }
            }
        }
        
        
        /**
         * Sets the cursor to a next element.
         *
         * @return boolean  <code>true</code> if cursor moved to the next element, otherwise <code>false</code>
         */
        public function Next() {
            if ( !parent::Next() ) {
                return false;
            }


            $this->resource->data_seek( $this->cursor );
            if ( empty( $this->data[$this->cursor] ) ) {
                $this->data[$this->cursor] = $this->resource->fetch_array();
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
                $this->resource->data_seek( $this->cursor );
                
                if ( empty( $this->data[$this->cursor] ) ) {
                    $this->data[$this->cursor] = $this->resource->fetch_array();
                }

                return true;
            }
            
            $this->cursor++;
            return false;
        }


        /**
         * Gets parameters of the current row and specified field as it.
         *
         * @param string $name  Field name.
         * @return string  Field value of the current row.
         */
        public function GetParameter( $name ) {
            if ( isset( $this->data[$this->cursor][$name] ) ) {
                return $this->data[$this->cursor][$name];
            }

            return null;
        }
        

        /**
         * Returns the string from the hash.
         *
         * @param  string $name  the parameter name
         * @return string parameter value
         */
        public function GetString( $name ) {
            $param = $this->GetParameter( $name );
            
            return MySqliConvert::FromString( $param );
        }
    
        
        /**
         * Returns the integer from the hash.
         *
         * @param  string $name  the parameter name
         * @return integer parameter value
         */
        public function GetInteger( $name ) {
            $param = $this->GetParameter( $name );
            
            return MySqliConvert::FromInteger( $param );
        }
        
        
        /**
         * Returns the float from the hash.
         *
         * @param  string $name  the parameter name
         * @return float parameter value
         */
        public function GetFloat( $name ) {
            $param = $this->GetParameter( $name );
                        
            return MySqliConvert::FromFloat( $param );
        }
        
        
        /**
         * Returns the double from the hash.
         *
         * @param  string $name  the parameter name
         * @return float parameter value
         */
        public function GetDouble( $name ) {
            $param = $this->GetParameter( $name );
                        
            return MySqliConvert::FromDouble( $param );
        }
    
        
        /**
         * Returns the boolean from the hash.
         *
         * @param  string $name  the parameter name
         * @return bool parameter value
         */
        public function GetBoolean( $name ) {
            $param = $this->GetParameter( $name );
            
            return MySqliConvert::FromBoolean( $param );
        }
    
    
        /**
         * Returns the datetime parameter from hash.
         *
         * @param string $name  Field name.
         * @return DateTimeWrapper
         */
        function GetDateTime( $name ) {
            $param = $this->getParameter( $name );
                        
            return MySqliConvert::FromDateTime( $param );
        }

        /**
         * @param string $name
         * @param string $type
         * @return mixed
         */
        public function GetValue( $name, $type = TYPE_STRING ) {
            return MySqliConvert::FromParameter( $this->data[$this->cursor][$name], $type );
        }


        /**
         * Get Affected Rows
         * @return int
         */
        public function GetAffectedRows() {
            $result   = null;
            $resource = $this->connection->GetResource();
            if ( $resource ) {
                /** @var $resource mysqli */
                $result = $resource->affected_rows;
            }

            return $result;
        }


        /**
         * Desturctor
         */
        public function __destruct() {
            if ( $this->resource instanceof mysqli_result ) {
                $this->resource->free();
            }
        }
    }
?>