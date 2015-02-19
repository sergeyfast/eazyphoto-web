<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Get Photo List Action
     *
     * @package EazyPhoto
     * @subpackage Albums
     * @property Photo[] list
     */
    class GetPhotoListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new PhotoFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $albums = AlbumFactory::Get( [], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'albums', $albums );
        }
    }
