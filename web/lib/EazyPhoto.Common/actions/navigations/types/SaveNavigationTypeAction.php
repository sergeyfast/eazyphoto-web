<?php

    use Eaze\Model\BaseFactory;

    /**
     * Save NavigationType List Action
     *
     * @package %project%
     * @subpackage Common
     * @property NavigationType originalObject
     * @property NavigationType currentObject
     * @property NavigationTypeFactory factory
     */
    class SaveNavigationTypeAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new NavigationTypeFactory();
        }


        /**
         * Form Object From Request
         *
         * @param NavigationType $originalObject
         * @return NavigationType
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var NavigationType $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->navigationTypeId = $originalObject->navigationTypeId;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param NavigationType $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param NavigationType $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param NavigationType $object
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
