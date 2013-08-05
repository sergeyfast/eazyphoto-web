<?php
    /**
     * Get Photo List Action
     * 
     * @package EazyPhoto
     * @subpackage Albums
     * @property Photo[] list
     */
    class GetPhotoListAction extends BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => false
            );

            parent::$factory = new PhotoFactory();
        }
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $albums = AlbumFactory::Get( null, array( BaseFactory::WithoutPages => true ) );
            Response::setArray( "albums", $albums );
        }
    }
?>