<?php
    /**
     * Get User List Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class GetUserListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            parent::$factory = new UserFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
?>