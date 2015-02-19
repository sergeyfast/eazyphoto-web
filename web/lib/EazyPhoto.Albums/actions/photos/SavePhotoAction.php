<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Save Photo List Action
     *
     * @package EazyPhoto
     * @subpackage Albums
     * @property Photo originalObject
     * @property Photo currentObject
     * @property PhotoFactory factory
     */
    class SavePhotoAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new PhotoFactory();
        }


        /**
         * Form Object From Request
         *
         * @param Photo $originalObject
         * @return Photo
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var Photo $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->photoId = $originalObject->photoId;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param Photo $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param Photo $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param Photo $object
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
            $albums = AlbumFactory::Get( [], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'albums', $albums );
        }
    }
