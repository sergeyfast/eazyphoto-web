<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Save Navigation List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property Navigation originalObject
     * @property Navigation currentObject
     * @property NavigationFactory factory
     */
    class SaveNavigationAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new NavigationFactory();
        }


        /**
         * Form Object From Request
         *
         * @param Navigation $originalObject
         * @return Navigation
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var Navigation $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->navigationId = $originalObject->navigationId;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param Navigation $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param Navigation $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param Navigation $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );

            return $result;
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
