<?php
    /**
     * StaticPage
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

        /** @var string */
        public $pageTitle;

        /** @var string */
        public $metaKeywords;

        /** @var string */
        public $metaDescription;

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
    }
?>