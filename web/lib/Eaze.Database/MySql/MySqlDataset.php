<?php
    /**
     * Helps to manage sets of MySQL database data resources.
     *
     * @package Eaze
     * @subpackage MySql
     * @author  max3.05
     */
    class MySqlDataSet extends DataSet {

        /**
         * Initializing instance.
         *
         * @param resource $resource  mysql resource.
         * @param IConnection $connection
         * @return MySqlDataSet
         */
        public function __construct(  $resource, IConnection $connection ){
            parent::__construct( $connection );

            if ( is_resource( $resource ) ) {
                $this->resource = $resource;
                $this->size     = mysql_num_rows( $resource );

                $i = mysql_num_fields( $resource );
                for ( $j = 0; $j < $i; $j++ ) {
                    $name = mysql_field_name( $resource, $j);
                    $this->Columns[$name] = $name;
                }
            }
        }
        
        
        /**
         * Sets the cursor to a next element.
         *
         * @return boolean  <code>ture</code> if cursor moved to the next element, otherwise <code>false</code>
         */
        public function Next() {
            if ( !parent::Next() ) {
                return false;
            }
            
            mysql_data_seek( $this->resource, $this->cursor );
            if ( empty( $this->data[$this->cursor] ) ) {
                $this->data[$this->cursor] = mysql_fetch_array( $this->resource, MYSQL_BOTH );
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
                mysql_data_seek( $this->resource, $this->cursor );
                
                if ( true == empty( $this->data[$this->cursor] ) ) {
                    $this->data[$this->cursor] = mysql_fetch_array( $this->resource, MYSQL_BOTH );
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
         * @return the parameter value
         */
        public function GetString( $name ) {
            $param = $this->GetParameter( $name );
            
            return MySqlConvert::FromString( $param );
        }
    
        
        /**
         * Returns the integer from the hash.
         *
         * @param  string $name  the parameter name
         * @return the parameter value
         */
        public function GetInteger( $name ) {
            $param = $this->GetParameter( $name );
            
            return MySqlConvert::FromInteger( $param );
        }
        
        
        /**
         * Returns the float from the hash.
         *
         * @param  string $name  the parameter name
         * @return the parameter value
         */
        public function GetFloat( $name ) {
            $param = $this->GetParameter( $name );
                        
            return MySqlConvert::FromFloat( $param );
        }
        
        
        /**
         * Returns the double from the hash.
         *
         * @param  string $name  the parameter name
         * @return the parameter value
         */
        public function GetDouble( $name ) {
            $param = $this->GetParameter( $name );
                        
            return MySqlConvert::FromDouble( $param );
        }
    
        
        /**
         * Returns the boolean from the hash.
         *
         * @param  string $name  the parameter name
         * @return the parameter value
         */
        public function GetBoolean( $name ) {
            $param = $this->GetParameter( $name );
            
            return MySqlConvert::FromBoolean( $param );
        }
    
    
        /**
         * Returns the datetime parameter from hash.
         *
         * @param string $name  Field name.
         * @return Datetime
         */
        function GetDateTime( $name ) {
            $param = $this->getParameter( $name );
                        
            return MySqlConvert::FromDateTime( $param );
        }

        /**
         * @param string $name
         * @param string $type
         * @return mixed
         */
        public function GetValue( $name, $type = TYPE_STRING ) {
            return MySqlConvert::FromParameter( $this->data[$this->cursor][$name], $type );
        }


        /**
         * Get Affected Rows
         * @return int
         */
        public function GetAffectedRows() {
            $result   = null;
            $resource = $this->connection->GetResource();
            if ( $resource ) {
                $result = mysql_affected_rows( $resource );
            }

            return $result;
        }

    }
?>