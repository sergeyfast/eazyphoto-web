<?php
    /**
     * Save StaticPage Action
     */
    class SaveStaticPageAction extends BaseSaveAction  {
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => true
            );

            parent::$factory = new StaticPageFactory();
        }

               
        /**
         * Form Object From Request
         *
         * @param StaticPage $originalObject
         * @return StaticPage
         */
        protected function getFromRequest( $originalObject = null ) {
            $object = parent::$factory->GetFromRequest();
            
            if ( $originalObject != null ) {
                $object->staticPageId = $originalObject->staticPageId;
            }

            return $object;
        }
        
        
        /**
         * Validate Object
         *
         * @param StaticPage $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );
            
            return $errors;
        }
        
        
        /**
         * Add Object
         *
         * @param StaticPage $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object );
            
            return $result;
        }
        
        
        /**
         * Update Object
         *
         * @param StaticPage $object
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
            $staticPages = StaticPageUtility::GetCollapsedData();
            Response::setArray( "staticPages", $staticPages );
        }
    }
?>