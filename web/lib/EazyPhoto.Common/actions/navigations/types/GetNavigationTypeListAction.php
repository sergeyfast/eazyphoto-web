<?php
    /**
     * Get NavigationType List Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class GetNavigationTypeListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            parent::$factory = new NavigationTypeFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
?>