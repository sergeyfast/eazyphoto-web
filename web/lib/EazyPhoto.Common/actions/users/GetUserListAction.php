<?php

    use Eaze\Model\BaseFactory;

    /**
     * Get User List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property User[] list
     */
    class GetUserListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new UserFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
