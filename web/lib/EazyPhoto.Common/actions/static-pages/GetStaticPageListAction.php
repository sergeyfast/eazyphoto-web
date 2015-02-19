<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Get StaticPage List Action
     *
     * @package EazyPhoto
     * @subpackage Common
     * @property StaticPage[] list
     */
    class GetStaticPageListAction extends Eaze\Model\BaseGetAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithoutDisabled  => false,
                BaseFactory::WithLists        => false,
            ];

            parent::$factory = new StaticPageFactory();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $staticPages = StaticPageUtility::GetData();
            Response::setArray( 'staticPages', $staticPages );
        }
    }
