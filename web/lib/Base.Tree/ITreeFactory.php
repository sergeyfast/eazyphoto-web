<?php
    /**
     * Tree Factory Abstract Class.
     *
     * @package Base
     * @subpackage Tree
     * @author Rykin Maxim
     */
    interface ITreeFactory {

        /**
         * Selects all children of the specified tree node.
         *
         * @param BaseTreeObject $object  Root tree object.
         * @param array $mapping          Mapping of the object.
         * @param string $connectionName  Name of the database connection to use
         * @static
         * @return array
         */
        public static function Get( $searchArray = array(), $options = array(), $object = null, $mapping, $connectionName = null );


        /**
         * Get node element by id.
         *
         * @param integer $id             Id of the object.
         * @param array $searchArray      Search array.
         * @param array $options          Array of the options to use.
         * @param BaseTreeObject $object  Root object to use.
         * @param array $mapping          Mapping for the object.
         * @param string $connectionName  Name of hte database connection to use.
         * @param string $mode            Mode of the tree storage.
         * @return BaseTreeObject
         */
        public static function GetById( $id, $searchArray, $options, $object, $mapping, $connectionName );


        /**
         * Gets one of the tree elements.
         *
         * @param array $searchArray      Search array.
         * @param array $options          Array of the options to use.
         * @param array $mapping          Mapping for the object.
         * @param string $connectionName  Name of hte database connection to use.
         * @param string $mode            Mode of the tree storage.
         * @return BaseTreeObject
         */
        public static function GetOne( $searchArray = array(), $options = array(), $mapping = array(), $connectionName = null );


        /**
         * Selects count of the element.
         *
         * @param BaseTreeObject $object  Root tree object.
         * @param array $mapping          Mapping of the object.
         * @param string $connectionName  Name of the database connection to use.
         */
        public static function Count( $searchArray = null, $object = null, $mapping, $connectionName = "" );


        /**
         * Adds new object to the tree.
         *
         * @param BaseTreeObject $object  Tree object to add.
         * @param array $mapping          Mapping of the object.
         * @param string $connectionName  Connection name to use.
         */
        public static function Add( $object, array $mapping, $connectionName = "" );


        /**
         * Deletes specified tree node.
         *
         * @param BaseTreeObject $object  Tree node to delete.
         * @param array $mapping          Mapping of the object
         * @param string $connectionName  Name of the database connection.
         * @param bool $withObjects       Determines whether deletes objects form the data table.
         */
        public static function Delete( $object, $mapping, $connectionName = "", $withObjects = true );


        /**
         * Moves tree node to the other node.
         *
         * @param BaseTreeNode $object       Tree node to move.
         * @param BaseTreeNode $destination  Destination tree node to move.
         * @param array $mapping             Mapping of the object.
         * @param string $connectionName     Name of the database connection to use.
         */
        public static function Move( $object, $destination, $mapping, $connectionName = null );


        /**
         * Copies tree node to the other node.
         *
         * @param BaseTreeNode $object       Tree node to copy.
         * @param BaseTreeNode $destination  Destination tree node to copy.
         * @param array $mapping             Mapping of the object.
         * @param string $connectionName     Name of the database connection to use.
         */
        public static function Copy( $object, $destination, $mapping, $connectionName = null );


        /**
         * Updates tree node data and/or tree structure
         *
         * @param mixed $object           node to update.
         * @param mixed $destination      Parent node for the target instance.
         * @param array $mapping          Object mapping.
         * @param string $connectionName  Name of the database connection to use.
         * @param mode $mode              Tree mode.
         */
        public static function Update( $object, $destination, $mapping, $connectionName = null );


        /**
         * Validates the tree.
         *
         * @param array $mapping          Object mapping.
         * @param string $connectionName  Name of the database connection to use.
         */
        public static function Check( $mapping, $connectionName = "" );


        /**
         * Restore tree from the base table.
         *
         * @param array $mapping
         * @param string $connectionName Name of the database connection to use.
         */
        public static function Restore( $mapping, $connectionName = "" );

        /**
         * Gets the node branch.
         *
         * @param BaseTreeNode $object          Start node to get branch.
         * @param array        $mapping         Object mapping to use.
         * @param string       $connectionName  Name of the connection to use.
         */
        public static function GetBranch( $object, $mapping, $connectionName = null );


        /**
         * Gets children nodes for specified level.
         *
         * @param BaseTreeNode $object    Parent tree node.
         * @param array $searchArray      Array of the search parameters.
         * @param array $options          Array of the options to use.
         * @param integer $level          Max level to get the children.
         * @param string $connectionName  Name of the database connection to use.
         * @param string $mode            Mode to use.
         * @return array
         */
        public static function GetChildren( $object, $searchArray = array(), $options = array(), $level = 1, $mapping, $connectionName = null );
    }
?>