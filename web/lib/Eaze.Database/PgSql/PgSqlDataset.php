<?php
    /**
     * Helps to manage sets of PostgreSQL database data resources.
     *
     * @package Eaze
     * @subpackage PgSql
     * @author max3.05, sergeyfast
     */
    class PgSqlDataSet extends DataSet {

        /**
         * Initializing instance.
         *
         * @param resource $resource  PostgreSQL result resource.
         * @param IConnection $connection
         * @return PgSqlDataSet
         */
        public function __construct( $resource, IConnection $connection ) {
            if ( is_resource( $resource ) ) {
                $this->size     = pg_num_rows( $resource );
                $this->resource = $resource;
                
                $i = pg_num_fields( $resource );
                for ( $j = 0; $j < $i; $j++ ) {
                    $name = pg_field_name( $resource, $j );
                    $this->Columns[$name] = $name;
                }

                parent::__construct( $connection );
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
            
            if ( empty( $this->data[$this->cursor] ) ) {
                $this->data[$this->cursor] = pg_fetch_array( $this->resource, $this->cursor, PGSQL_BOTH );
            }

            return true;
        }
        
        
        /**
         * Sets the cursor to a previous element.
         *
         * @return boolean  <code>true</code> if cursor moved to the previous element, otherwise <code>false</code>
         */
        public function Previous() {
            $this->cursor --;
            
            if ( $this->cursor > -1 ) {
                if ( empty( $this->data[$this->cursor] ) ) {
                    $this->data[$this->cursor] = pg_fetch_array( $this->resource, $this->cursor, PGSQL_BOTH );
                }

                return true;
            }
            
            $this->cursor ++;
            return false;
        }


        /**
         * Gets parameters of the current row and specified field as it.
         *
         * @param string $name  Field name.
         * @return string Field value of the current row.
         */
        public function GetParameter( $name ) {
            if ( isset( $this->data[$this->cursor][$name] ) ) {
                return $this->data[$this->cursor][$name];
            } 
            
            return null;
        }
        

        /**
         * @param string $name
         * @param string $type
         * @return mixed
         */
        public function GetValue( $name, $type = TYPE_STRING ) {
            return PgSqlConvert::FromParameter( $this->data[$this->cursor][$name], $type );
        }


        /**
         * Returns the string from the hash.
         *
         * @param  string $name  the parameter name
         * @return string the parameter value
         */
        public function GetString( $name ) {
            $param = $this->GetParameter( $name );
            
            return PgSqlConvert::FromString( $param );
        }
    
        
        /**
         * Returns the integer from the hash.
         *
         * @param  $name  the parameter name
         * @return int parameter value
         */
        public function GetInteger( $name ) {
            $param = $this->GetParameter( $name );
            
            return PgSqlConvert::FromInteger( $param );
        }
        
        
        /**
         * Returns the float from the hash.
         *
         * @param  $name  the parameter name
         * @return float parameter value
         */
        public function GetFloat( $name ) {
            $param = $this->GetParameter( $name );
                        
            return PgSqlConvert::FromFloat( $param );
        }
        
        
        /**
         * Returns the double from the hash.
         *
         * @param  $name  the parameter name
         * @return float parameter value
         */
        public function GetDouble( $name ) {
            $param = $this->GetParameter( $name );
                        
            return PgSqlConvert::FromDouble( $param );
        }
    
        
        /**
         * Returns the boolean from the hash.
         *
         * @param  $name  the parameter name
         * @return the parameter value
         */
        public function GetBoolean( $name ) {
            $param = $this->getParameter( $name );
            
            return PgSqlConvert::FromBoolean( $param );
        }
    
    
        /**
         * Returns the datetime parameter from hash.
         *
         * @param string $name  Field name.
         * @return Datetime
         */
        public function GetDateTime( $name ) {
            $param = $this->getParameter( $name );
                        
            return PgSqlConvert::FromDateTime( $param );
        }


        /**
         * Get Affected Rows
         * @return int
         */
        public function GetAffectedRows() {
            $result = null;
            if ( $this->resource ) {
                $result = pg_affected_rows( $this->resource );
            }

            return $result;
        }

    }
?>