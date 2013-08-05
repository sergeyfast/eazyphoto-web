<?php
    /**
     * Save DaemonLock Action
     * 
     */
    class SaveDaemonLockAction extends BaseSaveAction  {
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                "hideDisabled" => false
                , "withLists"  => true
            );

            parent::$factory = new DaemonLockFactory();
        }

               
        /**
         * Form Object From Request
         *
         * @return DaemonLock
         */
        protected function getFromRequest( $originalObject = null ) {
            $object = parent::$factory->GetFromRequest();
            
            if ( $originalObject != null ) {
                $object->daemonLockId = $originalObject->daemonLockId;
            }
            
            return  $object;
        }


        /**
         * Get Search Array
         *
         * @return array
         */
        protected function getSearch() {
            $search = array();
            
            return $search;
        }
        
        
        /**
         * Validate Object
         *
         * @param object $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );
            
            return $errors;
        }
        
        
        /**
         * Add Object
         *
         * @param object $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object );
            
            return $result;
        }
        
        
        /**
         * Update Object
         *
         * @param object $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );
            
            return $result;
        }
        
        
        /**
         * Set Foreign Lists
         *
         */
        protected function setForeignLists() {
        }
    }
?>