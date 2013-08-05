<?php
    /**
     * Save MetaDetail Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class SaveMetaDetailAction extends BaseSaveAction  {
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => true
            );

            parent::$factory = new MetaDetailFactory();
        }

               
        /**
         * Form Object From Request
         *
		 * @param MetaDetail $originalObject 
         * @return MetaDetail
         */
        protected function getFromRequest( $originalObject = null ) {
            $object = parent::$factory->GetFromRequest();
            
            if ( $originalObject != null ) {
                $object->metaDetailId = $originalObject->metaDetailId;
            }
            
            return $object;
        }
        
        
        /**
         * Validate Object
         *
         * @param MetaDetail $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );
            
            return $errors;
        }
        
        
        /**
         * Add Object
         *
         * @param MetaDetail $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object );
            
            return $result;
        }
        
        
        /**
         * Update Object
         *
         * @param MetaDetail $object
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