<?php


    /**
     * StaticPage
     *
     * @package EazyPhoto
     * @subpackage Common
     */
    class StaticPage {

        /** @var int */
        public $staticPageId;

        /** @var string */
        public $title;

        /** @var string */
        public $url;

        /** @var string */
        public $content;

        /** @var int */
        public $orderNumber;

        /** @var int */
        public $parentStaticPageId;

        /** @var StaticPage */
        public $parentStaticPage;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;

        /** @var array */
        public $nodes;

        /** @var array */
        public $images;

        # user defined code goes below

    }
