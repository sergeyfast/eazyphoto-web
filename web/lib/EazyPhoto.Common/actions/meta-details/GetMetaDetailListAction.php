<?php
    /**
     * Get MetaDetail List Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class GetMetaDetailListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            parent::$factory = new MetaDetailFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
?>