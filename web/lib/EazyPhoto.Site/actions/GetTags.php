<?php
    use Eaze\Core\Response;

    /**
     * Get Tags Action
     * @package    EazyPhoto
     * @subpackage Site
     * @author     Sergeyfast
     */
    class GetTags {

        /**
         * Entry Point
         */
        public function Execute() {
            $tags = TagFactory::Get( [ 'nullParentTagId' => true, 'nnOrderNumber' => true ] );

            Context::$ActiveSection = Context::Tags;

            Response::setArray( 'tags', $tags );
        }
    }