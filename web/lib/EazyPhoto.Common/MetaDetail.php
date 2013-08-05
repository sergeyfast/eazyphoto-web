<?php
    /**
     * MetaDetail
     */
    class MetaDetail {

        /** @var int */
        public $metaDetailId;

        /** @var string */
        public $url;

        /** @var string */
        public $pageTitle;

        /** @var string */
        public $metaKeywords;

        /** @var string */
        public $metaDescription;

        /** @var string */
        public $alt;

        /** @var bool */
        public $isInheritable;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;
    }
?>