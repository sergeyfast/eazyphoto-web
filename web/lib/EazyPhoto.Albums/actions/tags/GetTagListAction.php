<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Get Tag List Action
     *
     * @package EazyPhoto
     * @subpackage Albums
     * @property Tag[] list
     */
    class GetTagListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new TagFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $tags = TagFactory::Get( [ 'nullParentTagId' => true ], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'parentTags', $tags );
        }
    }
