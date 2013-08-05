<?php
    /**
     * Save SiteParam Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class SaveSiteParamAction extends BaseSaveAction  {
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => true
            );

            parent::$factory = new SiteParamFactory();
        }

               
        /**
         * Form Object From Request
         *
		 * @param SiteParam $originalObject 
         * @return SiteParam
         */
        protected function getFromRequest( $originalObject = null ) {
            $object = parent::$factory->GetFromRequest();
            
            if ( $originalObject != null ) {
                $object->siteParamId = $originalObject->siteParamId;
            }
            
            return $object;
        }
        
        
        /**
         * Validate Object
         *
         * @param SiteParam $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );
            
            return $errors;
        }
        
        
        /**
         * Add Object
         *
         * @param SiteParam $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object );
            
            return $result;
        }
        
        
        /**
         * Update Object
         *
         * @param SiteParam $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );
            
            return $result;
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
?>