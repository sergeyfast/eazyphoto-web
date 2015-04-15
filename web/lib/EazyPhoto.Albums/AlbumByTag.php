<?php


    /**
     * Album By Tag
     *
     * @package    EazyPhoto
     * @subpackage Albums
     */
    class AlbumByTag {

        /**
         * @var int
         */
        public $TagId;

        /**
         * @var Tag
         */
        public $Tag;

        /**
         * @var int[]
         */
        public $AlbumIds;

        /**
         * @var Album[]
         */
        public $Albums = [];

        /**
         * @var int
         */
        public $Count;
    }