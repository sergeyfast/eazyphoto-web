<?php

    use Eaze\Model\BaseFactory;

    /**
     * Get MetaDetail List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property MetaDetail[] list
     */
    class GetMetaDetailListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new MetaDetailFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
