<?php


    /**
     * Base Tree Object Factory
     *
     * @package    Base
     * @subpackage Base.Tree
     * @author     Rykin Maxim
     */
    class BaseTreeFactory {

        /**
         * Mapping For Base Tree Object.
         *
         * @var array
         * @access public
         * @static
         */
        public static $mapping = [
            "fields"   => [
                "objectId"   => [
                    "name"   => "objectId"
                    , "type" => TYPE_INTEGER
                    , "key"  => true
                ]
                , "parentId" => [
                    "name"         => "parentId"
                    , "type"       => TYPE_INTEGER
                    , "foreignKey" => "BaseTreeObject"
                ]
                , "path"     => [
                    "name"       => "path"
                    , "type"     => TYPE_STRING
                    , "nullable" => "CheckEmpty"
                ]
                , "rKey"     => [
                    "name"   => "rKey"
                    , "type" => TYPE_INTEGER
                ]
                , "lKey"     => [
                    "name"   => "lKey"
                    , "type" => TYPE_INTEGER
                ]
                , "level"    => [
                    "name"   => "level"
                    , "type" => TYPE_INTEGER
                ]
            ]
            , "search" => [
                "_parentId" => [
                    "name"         => "parentId"
                    , "type"       => TYPE_INTEGER
                    , "searchType" => SEARCHTYPE_ARRAY
                ]
            ]
        ];

        /**
         * Current storage mode.
         *
         * @var string
         * @access private
         * @static
         */
        public static $CurrentMode = TREEMODE_LTREE;

        /**
         * Modes supported by add/update methods.
         *
         * @var array.
         * @access private
         * @static
         */
        private static $supportedModes = [ ];


        /**
         * Sets current storage mode.
         *
         * @param string $mode Mode to set.
         * @access public
         * @static
         */
        public static function SetCurrentMode( $mode ) {
            self::$CurrentMode = $mode;
        }


        /**
         * Sets supported modes for add/update actions.
         *
         * @param array $modes
         * @access public
         * @static
         */
        public static function SetSupportedModes( $modes ) {
            self::$supportedModes = $modes;
        }


        /**
         * Selects all children of the specified tree node.
         *
         * @param BaseTreeObject $object         Root tree object.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Name of the database connection to use
         * @static
         * @return array
         */
        public static function Get( $searchArray = [ ], $options = [ ], $object = null, $mapping, $connectionName = "" ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->Get( $searchArray, $options, $object, $mapping, $connectionName ) );
        }


        /**
         * Get node element by id.
         *
         * @param integer        $id             Id of the object.
         * @param array          $searchArray    Search array.
         * @param array          $options        Array of the options to use.
         * @param BaseTreeObject $object         Root object to use.
         * @param array          $mapping        Mapping for the object.
         * @param string         $connectionName Name of hte database connection to use.
         * @param string         $mode           Mode of the tree storage.
         * @return BaseTreeObject
         */
        public static function GetById( $id, $searchArray, $options, $object, $mapping, $connectionName ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->GetById( $id, $searchArray, $options, $object, $mapping, $connectionName ) );
        }


        /**
         * Gets one of the tree elements.
         *
         * @param array  $searchArray    Search array.
         * @param array  $options        Array of the options to use.
         * @param array  $mapping        Mapping for the object.
         * @param string $connectionName Name of hte database connection to use.
         * @param string $mode           Mode of the tree storage.
         * @return BaseTreeObject
         */
        public static function GetOne( $searchArray = [ ], $options = [ ], $mapping = [ ], $connectionName = null ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->GetOne( $searchArray, $options, $mapping, $connectionName ) );
        }


        /**
         * Selects count of the element.
         *
         * @param BaseTreeObject $object         Root tree object.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Name of the database connection to use.
         */
        public static function Count( $searchArray = null, $mapping, $options = null, $connectionName = "" ) {
            $factoryName = self::$CurrentMode . "Factory";
            $factory     = new $factoryName();

            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->Count( $searchArray, $options, $mapping, $connectionName ) );
        }


        /**
         * Adds new object to the tree.
         *
         * @param BaseTreeObject $object         Tree object to add.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Connection name to use.
         */
        public static function Add( $object, array $mapping, $connectionName = "" ) {
            $factoryName = self::$CurrentMode . "Factory";
            $factory     = new $factoryName();

            return ( $factory->Add( $object, $mapping, $connectionName ) );
        }


        /**
         * Deletes specified tree node.
         *
         * @param BaseTreeObject $object         Tree node to delete.
         * @param array          $mapping        Mapping of the object
         * @param string         $connectionName Name of the database connection.
         * @param bool           $withObjects    Determines whether deletes objects form the data table.
         */
        public static function Delete( $object, $mapping, $connectionName = "", $withObjects = true ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->Delete( $object, $mapping, $connectionName, $withObjects ) );
        }


        /**
         * Moves tree node to the other node.
         *
         * @param BaseTreeObject $object         Tree node to move.
         * @param BaseTreeObject $destination    Destination tree node to move.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Name of the database connection to use.
         */
        public static function Move( $object, $destination, $mapping, $connectionName = null ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->Move( $object, $destination, $mapping, $connectionName ) );
        }


        /**
         * Copies tree node to the other node.
         *
         * @param BaseTreeObject $object         Tree node to copy.
         * @param BaseTreeObject $destination    Destination tree node to copy.
         * @param array          $mapping        Mapping of the object.
         * @param string         $connectionName Name of the database connection to use.
         */
        public static function Copy( $object, $destination, $mapping, $connectionName = null ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->Copy( $object, $destination, $mapping, $connectionName ) );
        }


        /**
         * Updates tree node data and/or tree structure
         *
         * @param mixed  $object         node to update.
         * @param mixed  $destination    Parent node for the target instance.
         * @param array  $mapping        Object mapping.
         * @param string $connectionName Name of the database connection to use.
         */
        public static function Update( $object, $destination, $mapping, $connectionName = null ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return ( $factory->Update( $object, $destination, $mapping, $connectionName ) );
        }


        /**
         * Gets the branch of the specified node.
         *
         * @param BaseTreeObject $object         Object to get branch.
         * @param array          $mapping        Object mapping array.
         * @param string         $connectionName Connection name to use in query.
         * @return array
         */
        public static function GetBranch( $object, $mapping, $connectionName = null ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return $factory->GetBranch( $object, $mapping, $connectionName );
        }


        /**
         * Gets children nodes for specified level.
         *
         * @param BaseTreeObject $object         Parent tree node.
         * @param array          $searchArray    Array of the search parameters.
         * @param array          $options        Array of the options to use.
         * @param integer        $level          Max level to get the children.
         * @param string         $connectionName Name of the database connection to use.
         * @return array
         */
        public static function GetChildren( $object, $searchArray = [ ], $options = [ ], $level = 1, $mapping, $connectionName = null ) {
            $factoryName       = self::$CurrentMode . "Factory";
            $factory           = new $factoryName();
            $mapping["fields"] = array_merge( $mapping["fields"], self::$mapping["fields"] );
            $mapping["search"] = array_merge( $mapping["search"], self::$mapping["search"] );

            return $factory->GetChildren( $object, $searchArray, $options, $level, $mapping, $connectionName );
        }


        /**
         * Validates the tree.
         *
         * @param array  $mapping        Object mapping.
         * @param string $connectionName Name of the database connection to use.
         * @return bool
         */
        public static function Check( $mapping, $connectionName = "" ) {
            return false;
        }


        /**
         * Restore tree from the base table.
         *
         * @param array  $mapping
         * @param string $connectionName Name of the database connection to use.
         * @return bool
         */
        public static function Restore( $mapping, $connectionName = "" ) {
            return false;
        }
    }