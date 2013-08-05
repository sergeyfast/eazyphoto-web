<?php
    /**
     * Get StaticPage List Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class GetStaticPageListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            $this->sortFields = array( 'parentStaticPage.title' );

            parent::$factory = new StaticPageFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $staticPages = StaticPageUtility::GetData();
            Response::setArray( "staticPages", $staticPages );
        }
    }
?>