<?php
    /**
     * Base Tree Object
     * 
     * @package Base
     * @subpackage Base.Tree
     * @author Rykin Maxim
     */
    class BaseTreeObject {
        /**
         * Object id.
         *
         * @var int
         */
        public $objectId = null;
        
        /**
         * Parent object id.
         *
         * @var int
         */
        public $parentId = null;
        
        /**
         * Parent tree node.
         * 
         * @var BaseTreeObject.
         */
        public $parent = null;
        
        /**
         * Material path to the node.
         *
         * @var string
         */
        public $path = "";
        
        /**
         * Right key for nested sets.
         *
         * @var int
         */
        public $rKey = 0;
        
        /**
         * Left key for nested sets.
         *
         * @var int
         */
        public $lKey = 0;
        
        /**
         * Node level in the tree.
         *
         * @var int
         */
        public $level = 0;
        
        /**
         * Children nodes list.
         *
         * @var array
         */
        public $nodes = array();
        
        
        /**
         * Represents tree branch as array of the tree node.
         *
         * @return array
         */
        public function GetBranch() {
            $result = array();
            
            while ( !empty( $obj->parent ) ) {
                $result[] = clone $obj->parent;
                $obj = $obj->parent;
            }
            
            $result = array_reverse( $result );
            
            return $result;
        }
        
        public function GetParentPath() {
            $return = "";
            
            $path = $this->path;

            if ( !empty( $path ) ) {
                $pathes = explode( '.', $this->path );
                $return = "";
                
                for ( $i = 0; $i < count( $pathes ) - 1; $i++ ) {
                    $return .= $pathes[$i] . ".";
                }
                
                return ( rtrim( $return, '.' ) );
            } else if ( !empty( $this->parent ) ) {
                return ( $this->parent->path );
            }
            
            return $return;
        }
        
        
        public function GetFormattedPath( $format = "_" ) {
            return str_replace( ".", $format, $this->path );
        }
    }
?>