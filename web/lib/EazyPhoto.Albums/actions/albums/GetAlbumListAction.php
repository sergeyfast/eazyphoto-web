<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Get Album List Action
     *
     * @package EazyPhoto
     * @subpackage Albums
     * @property Album[] list
     */
    class GetAlbumListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new AlbumFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $users = UserFactory::Get( [], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'users', $users );
        }
    }
