<?php
    /**
     * Database connection interface
     * 
     * @package Eaze
     * @subpackage Database
     * @author max3.05
     */
    interface IConnection {
        
        /**
         * Executes specified query and return result dataset
         *
         * @param string $query  Sql query to execute.
         * @return IDataSet Result dataset
         */
        function ExecuteQuery( $query );
        
        /**
         * Execute Sql query and return result statement/
         *
         * @param string $query Sql query to execute.
         * @return boolean Return <code>true</code> if command executed successfully, otherwise return <code>false</code>.
         */
        function ExecuteNonQuery( $query );
        
        /**
         * Starts transaction
         * @return bool
         */
        function Begin();
        
        /**
         * Commits current transaction.
         * @return bool
         */
        function Commit();
        
        /**
         * Rollbacks current transaction.
         * @return bool
         */
        function Rollback();
        
        /**
         * Determines if transaction started.
         *
         * @return bool Return <code>true</code> if current connection is in transaction, otherwise <code>false</code>
         */
        function IsTransaction();

        /**
         * Gets last error message string of the connection.
         *
         * @return string Last message error string if the connection.
         */
        function GetLastError();
        
        /**
         * Opens connection using specified parameters
         */
        function Open();
        
        /**
         * Close current connection
         * @return bool
         */
        function Close();
        
        /**
         * Checks if current connection instance is opened.
         *
         * @return boolean  <code>True</code> if connection is opened, otherwise <code>false</code>.
         */
        function IsOpened();

        /**
         * Quote String
         *
         * @param string $str
         */
        function Quote( $str );

        /**
         * Get SqlConverter
         * @abstract
         * @return ISqlConvert
         */
        function GetConverter();


        /**
         * Get Complex Type
         * @abstract
         * @param  string $alias  (e.g. php, json, int[], string[], hstore)
         * @return IComplexType
         */
        function GetComplexType( $alias );


        /**
         * Get Connection Resource
         * @abstract
         * @return resource
         */
        function GetResource();

        /**
         * Get Connection Name
         * @abstract
         * @return string
         */
        function GetName();

        /**
         * Returns ClassName
         * @abstract
         * @return string
         */
        function GetClassName();

        /**
         * Get Last Insert Id (if applicable)
         * @abstract
         * @return integer
         */
        function GetLastInsertId();
    }
?>