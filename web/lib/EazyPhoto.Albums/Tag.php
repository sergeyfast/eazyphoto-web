<?php


    /**
     * Tag
     *
     * @package EazyPhoto
     * @subpackage Albums
     */
    class Tag {

        /** @var int */
        public $tagId;

        /** @var string */
        public $title;

        /** @var string */
        public $alias;

        /** @var string */
        public $description;

        /** @var int */
        public $orderNumber;

        /** @var string */
        public $photoPath;

        /** @var int */
        public $photoId;

        /** @var Photo */
        public $photo;

        /** @var int */
        public $parentTagId;

        /** @var Tag */
        public $parentTag;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;

        # user defined code goes below

        /**
         * Path
         * @var int[]
         */
        public $path;

        /**
         * Level
         * @var int
         */
        public $depth;

    }
