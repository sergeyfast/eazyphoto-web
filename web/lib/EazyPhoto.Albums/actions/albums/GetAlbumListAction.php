<?php
    /**
     * Get Album List Action
     * 
     * @package EazyPhoto
     * @subpackage Albums
     * @property Album[] list
     */
    class GetAlbumListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            parent::$factory = new AlbumFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $users = UserFactory::Get( null, array( BaseFactory::WithoutPages => true ) );
            Response::setArray( "users", $users );
        }
    }
?>