<?php

    use Eaze\Model\BaseFactory;

    /**
     * Get DaemonLock List Action
     *
     * @package %project%
     * @subpackage Common
     * @property DaemonLock[] list
     */
    class GetDaemonLockListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new DaemonLockFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
