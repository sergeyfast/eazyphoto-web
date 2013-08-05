<?php
    /**
     * Save Navigation Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class SaveNavigationAction extends BaseSaveAction  {
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => true
            );

            parent::$factory = new NavigationFactory();
        }

               
        /**
         * Form Object From Request
         *
		 * @param Navigation $originalObject 
         * @return Navigation
         */
        protected function getFromRequest( $originalObject = null ) {
            $object = parent::$factory->GetFromRequest();
            
            if ( $originalObject != null ) {
                $object->navigationId = $originalObject->navigationId;
            }
            
            return $object;
        }
        
        
        /**
         * Validate Object
         *
         * @param Navigation $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );
            
            return $errors;
        }
        
        
        /**
         * Add Object
         *
         * @param Navigation $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object );
            
            return $result;
        }
        
        
        /**
         * Update Object
         *
         * @param Navigation $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );
            
            return $result;
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $navigationTypes = NavigationTypeFactory::Get( null, array( BaseFactory::WithoutPages => true ) );
            Response::setArray( "navigationTypes", $navigationTypes );
            $staticPages = StaticPageUtility::GetCollapsedData();
            Response::setArray( "staticPages", $staticPages );
        }
    }
?>