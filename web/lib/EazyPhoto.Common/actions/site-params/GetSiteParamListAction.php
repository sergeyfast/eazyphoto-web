<?php

    use Eaze\Model\BaseFactory;

    /**
     * Get SiteParam List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property SiteParam[] list
     */
    class GetSiteParamListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new SiteParamFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
