<?php
    /**
     * Get SiteParam List Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class GetSiteParamListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            parent::$factory = new SiteParamFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
?>