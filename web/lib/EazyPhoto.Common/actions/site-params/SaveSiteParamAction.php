<?php

    use Eaze\Model\BaseFactory;

    /**
     * Save SiteParam List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property SiteParam originalObject
     * @property SiteParam currentObject
     * @property SiteParamFactory factory
     */
    class SaveSiteParamAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new SiteParamFactory();
        }


        /**
         * Form Object From Request
         *
         * @param SiteParam $originalObject
         * @return SiteParam
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var SiteParam $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->siteParamId = $originalObject->siteParamId;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param SiteParam $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param SiteParam $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param SiteParam $object
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
