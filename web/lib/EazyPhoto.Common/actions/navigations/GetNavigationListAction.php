<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Get Navigation List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property Navigation[] list
     */
    class GetNavigationListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new NavigationFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $navigationTypes = NavigationTypeFactory::Get( [], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'navigationTypes', $navigationTypes );
            $staticPages = StaticPageFactory::Get( [], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'staticPages', $staticPages );
        }
    }
