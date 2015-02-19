<?php

    use Eaze\Model\BaseFactory;

    /**
     * Get NavigationType List Action
     *
     * @package %project%
     * @subpackage Common
     * @property NavigationType[] list
     */
    class GetNavigationTypeListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new NavigationTypeFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
