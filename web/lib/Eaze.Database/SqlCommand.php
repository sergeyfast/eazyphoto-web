<?php
    /**
     * SqlCommand
     *
     * @package Eaze
     * @subpackage Database
     * @author  sergeyfast
     */
    class SqlCommand {

        /**
         * The sql command text.
         * 
         * @var string
         */
    	private $command;

    	/**
    	 * The connection descriptor.
    	 * 
    	 * @var IConnection
    	 */
        private $connection;
        
        /**
         * Sql command parameters.
         *
         * @var array
         */
        private $params = array();

        /**
         * @var string chr(254)
         */
        private static $atSymbol;


        /**
         * Sets the command and a connection.
         *
         * @param string      $command     the sql query
         * @param IConnection $connection  the object implements {@link IConnection}
         * @see IConnection
         */
        public function SqlCommand( $command, IConnection $connection ) {
            if ( !empty( $command ) ) {
                $this->SetCommand( $command );    
            }
            
            $this->connection = $connection;
            self::$atSymbol   = chr( 254 );
        }


        /**
         * Sets the command text.
         *
         * @param string $command  the command text
         */
        public function SetCommand( /*string*/ $command ) {
            $this->command = trim( $command );
            
            if ( empty( $this->command ) ) {
                Logger::Error( 'Empty sql command specified' );
            }
        }


        /**
         * @param string $key
         * @return array
         */
        public static function ReplaceAtSymbol( $key ) {
            if ( strpos( $key, '@' ) === 0 ) {
                $key[0] = self::$atSymbol;
            };

            return $key;
        }


        /**
         * Returns replaced sql statement with parameters
         * @return string
         */
        private function getPreparedQuery() {
            $params = $this->params;
            krsort( $params, SORT_STRING );

            $keys  = array_map( array( 'SqlCommand', 'ReplaceAtSymbol' ), array_keys( $params ) );
        	$query = str_replace( array_keys( $params ), $keys, $this->command );
            $query = str_replace( $keys, array_values( $params ), $query );

            return $query;
        }


        /**
         * Executes the command.
         *
         * @return DataSet
         */
        public function Execute() {
            if ( !is_callable( array( $this->connection, 'executeQuery' ) ) ) {
                Logger::Error( 'Wrong database connection specified' );

                return null;
            }

            $query = $this->getPreparedQuery();
            
            // Execute query and create DataSet
            Logger::Checkpoint();
            $data = $this->connection->executeQuery( $query );

            /** Log sql query */
            if ( Logger::GetOutputMode() ==  Logger::HtmlMode ) {
                Logger::Debug( nl2br( str_replace( ' ', '&nbsp;', $query ) ) );
            } else {
                Logger::Debug( $query );
            }
            
            return $data;
        }


        /**
         * Executes the non query command.
         *
         * @return bool
         */
        public function ExecuteNonQuery() {
            if ( !is_callable( array( $this->connection, "executeNonQuery" ) ) ) {
                Logger::Error( 'Wrong database connection specified' );

                return null;
            }

            $query = $this->getPreparedQuery();

            // Execute query and create DataSet
            Logger::Debug( $query );
            $result = $this->connection->executeNonQuery( $query );

            return $result;
        }

        
        /**
         * Gets query with replaced parameters.
         *
         * @return string  Sql query 
         */
        public function GetQuery() {
            return $this->getPreparedQuery();
        }
        
        
        /**
         * Removes value from parameters with specified name
         *
         * @param string $name  Name of the parameter.
         */
        public function ClearParameter( $name ) {
            if ( isset( $this->params[$name] ) ) {
                unset( $this->params[$name] );
            }
        }
        
        
        /**
         * Clears parameters array.
         *
         */
        public function ClearParameters() {
            $this->params = array();
        }
        
        
        /**
         * Sets parametrized string.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetString( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToString( $value );
        }
        
        
        /**
         * Sets parametrized integer.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetInt( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToInt( $value );
        }
        
        
        /**
         * Sets parametrized integer.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetInteger( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToInt( $value );
        }
        
        
        /**
         * Sets parametrized double.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetDouble( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToDouble( $value );
        }
        
        
        /**
         * Sets parametrized float.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetFloat( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToFloat( $value );
        }
        
        
        /**
         * Sets parametrized boolean.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetBoolean( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToBoolean( $value );
        }


        /**
         * Sets parametrized array.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         * @param string $type   Type of instances in the array.
         */
        public function SetList( $name, $value, $type ){
            $this->params[$name] = $this->connection->GetConverter()->ToList( $value, $type );
        }


        /**
         * Sets parametrized datetime.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetDateTime( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToDateTime( $value );
        }
        
        
        /**
         * Sets parametrized date.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetDate( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToDate( $value );
        }

        /**
         * Sets parametrized time.
         *
         * @param string $name   Name of the parameter.
         * @param string $value  Value to set.
         */
        public function SetTime( $name, $value ){
            $this->params[$name] = $this->connection->GetConverter()->ToTime( $value );
        }


        /**
         * Set Complex Type
         * @param  string $name   name of the parameter
         * @param  mixed  $value  value to set
         * @param  string $type   complex type alias
         * @return void
         */
        public function SetComplexType( $name, $value, $type ) {
            $ct = $this->connection->GetComplexType( $type );
            $this->params[$name] = $ct !== null ? $ct->ToDatabase( $value ) : $this->connection->GetConverter()->NullToString( null );
        }


        /**
         * Set parameter.
         *
         * @param string $name          Name of the parameter.
         * @param string $value         Value to set.
         * @param string $type          Value type.
         * @param string $complexType   optional complex type alias
         *
         */
        public function SetParameter( $name, $value, $type = TYPE_STRING, $complexType = null ){
            $method = 'Set' . $type;

            if ( !empty( $complexType ) ) {
                $this->SetComplexType( $name, $value, $complexType );
            } else if ( is_callable( array( $this, $method ) ) ) {
                $this->$method( $name, $value );
            } else {
                Logger::Error( 'Cannot call %s of class SqlCommand', $method );
            }
        }
        
        
        /**
         * Sets parameter as range filter.
         *
         * @param string $name      Name of the parameter.
         * @param mixed $lowBound   Low bound of the range.
         * @param mixed $highBound  High bound of the range.
         * @param string $type      Type of the range bounds.
         */
        public function SetRange( $name
                                  , $lowBound  = null
                                  , $highBound = null
                                  , $type      = TYPE_STRING ) {
            $method = 'To' . $type;
            
            if ( !is_callable( array( $this->connection->GetConverter(), $method ) ) ) {
                Logger::Error( 'Could not call method %s', $method );
            }
            
            if ( $lowBound === null && $highBound === null  ) {
                Logger::Error( 'Both bounds could not be null!' );
            } elseif ( $lowBound === null && ( $highBound !== null ) ) {
                $this->params[$name] = '<= ' . $this->connection->GetConverter()->$method( $highBound );
            } elseif ( $lowBound !== null && $highBound === null ) {
                $this->params[$name] = '>= ' . $this->connection->GetConverter()->$method( $lowBound );
            } else {
                $this->params[$name] = 'BETWEEN ' . $this->connection->GetConverter()->$method( $lowBound ) . ' AND ' . $this->connection->GetConverter()->$method( $highBound );
            }
        }
    }
?>