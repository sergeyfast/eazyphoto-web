<?php
    /**
     * Get Navigation List Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class GetNavigationListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            $this->sortFields = array( 'navigationType.title', 'staticPage.title' );

            parent::$factory = new NavigationFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $navigationTypes = NavigationTypeFactory::Get( null, array( BaseFactory::WithoutPages => true ) );
            Response::setArray( "navigationTypes", $navigationTypes );
            $staticPages = StaticPageUtility::GetCollapsedData();
            Response::setArray( "staticPages", $staticPages );
        }
    }
?>