<?php
    /**
     * Get DaemonLock List Action
     * 
     */
    class GetDaemonLockListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                "hideDisabled" => false
                , "withLists"  => false
            );

            parent::$factory = new DaemonLockFactory();
        }
        
        
        /**
         * Get Search Array
         *
         * @return array
         */
        protected function getSearch() {
            $search = Request::getArray( "search" );
            
            return $search;
        }
        
        
        /**
         * Set Foreign Lists
         *
         */
        protected function setForeignLists() {}
    }
?>