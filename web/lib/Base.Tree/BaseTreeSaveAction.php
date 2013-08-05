<?php
    /**
     * Base Abstract Save Action
     *
     * @author Sergey Bykov
     * @package Eaze
     * @subpackage Eaze.Model
     */
    abstract class BaseTreeSaveAction {
        /**
         * Current Factory
         *
         * @var IFactory
         */
        public static $factory; 
        
        /**
         * Current Object;
         *
         * @var object
         */
        protected $currentObject;
        
        /**
         * Original Object
         *
         * @var unknown_type
         */
        protected $originalObject;
         
        /**
         * Options for Get Object
         *
         * @var array
         */
        protected $options = array(
            "hideDisabled" => false
            , "withLists"  => true
        );
        
        
        /**
         * Abstract Add
         *
         * @param object $object
         */
        abstract protected function add( $object );
        
        
        /**
         * Abstract Update
         *
         * @param object $object
         */
        abstract protected function update( $object, $path = null );
        
        /**
         * Abstract Delete.
         *
         * @param object $object
         */
        abstract protected function delete( $object );
        
        
        /**
         * Abstract Validate
         *
         * @param object $object
         */
        abstract protected function validate( $object, $parentPath = null );
        
        
         /**
         * Abstract Get Search
         *
         * @return array
         */
        abstract protected function getSearch();
        
        
        /**
         * Abstract Get Object From Request
         *
         * @param object $originalObject
         */
        abstract protected function getFromRequest( $originalObject = null );
        
        
        /**
         * Abstract Set Foreign Lists
         *
         */
        abstract protected function setForeignLists();
        
        /**
         * Abstract Get Original Object.
         *
         */
        abstract protected function getOriginalObject();
        
        
        /**
         * Execute Action
         *
         * @return string
         */
        public function Execute() {
        	
            $addForm     = Request::getInteger( "addForm" );
            $editForm    = Request::getInteger( "editForm" );
            $deleteForm  = Request::getInteger( "deleteForm" );
            
            $mode = Request::getString( "mode" );
            $searchArray = $this->getSearch();
            $this->getOriginalObject();
            
            switch ( $mode ) {
                case "add" :
                    $object = $this->getFromRequest( $this->originalObject );
                    break;
                case "update" :
                    $object = $this->originalObject;
                    break;
                case "delete" :
                    $objectPath = Page::$RequestData[1];
                    $ids = explode( ".", $objectPath );

                    $object = self::$factory->GetById( $ids[count($ids) - 1] ); 
            }
            
            /** delete mode */
            if ( $deleteForm == 1 ) {
                $errors = $this->validate( $object );
                
                if ( !empty( $object ) && empty( $errors["root"] ) ) {
                    $this->delete( $object );
                }
                return null;
            }
            
            $this->setForeignLists();
            
            /** edit mode */
            if ( true == is_null( $object ) ) {
                $object = $this->getFromRequest();
            }
            
    
            //Add
            if ( (1 == $addForm) || (1 == $editForm) ) {
                $object = $this->getFromRequest( $this->originalObject );
                
                /// Proccess Validate
                $vars = get_class_vars( get_class( self::$factory ) );
                $class    = $vars["mapping"]["class"];
                $class[0] = strtolower( $class[0] );
                
                $array          = Request::getArray( $class );
                $path           = $array["parent.path"];
                
                $errors = $this->validate( $object, $path );
                
                if ( empty( $errors ) ) {
                    if ( $addForm == 1 ) {
                        $result = $this->add( $object );
                    } elseif( 1 == $editForm ) {
                        $result = $this->update( $object, $path );
                    }
                    
                    if ( $result === false ) {
                        $errors["fatal"] = "database";
                        Response::setParameter( "errors", $errors );
                    } else {
                        return "success";
                    }
                } else {
                    Response::setArray(  "errors", $errors );
                }
            }
            
            Response::setParameter( "object", $object );
        }
    }
?>