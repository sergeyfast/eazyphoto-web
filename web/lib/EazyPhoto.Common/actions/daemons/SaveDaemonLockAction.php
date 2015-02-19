<?php

    use Eaze\Model\BaseFactory;

    /**
     * Save DaemonLock List Action
     *
     * @package %project%
     * @subpackage Common
     * @property DaemonLock originalObject
     * @property DaemonLock currentObject
     * @property DaemonLockFactory factory
     */
    class SaveDaemonLockAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new DaemonLockFactory();
        }


        /**
         * Form Object From Request
         *
         * @param DaemonLock $originalObject
         * @return DaemonLock
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var DaemonLock $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->daemonLockId = $originalObject->daemonLockId;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param DaemonLock $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param DaemonLock $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param DaemonLock $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );

            return $result;
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
