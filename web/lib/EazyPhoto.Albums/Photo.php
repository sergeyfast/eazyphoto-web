<?php


    /**
     * Photo
     *
     * @package EazyPhoto
     * @subpackage Albums
     */
    class Photo {

        /** @var int */
        public $photoId;

        /** @var int */
        public $albumId;

        /** @var Album */
        public $album;

        /** @var string */
        public $originalName;

        /** @var string */
        public $filename;

        /** @var int */
        public $fileSize;

        /** @var int */
        public $fileSizeHd;

        /** @var int */
        public $orderNumber;

        /** @var string */
        public $afterText;

        /** @var string */
        public $title;

        /** @var array */
        public $exif;

        /** @var \Eaze\Core\DateTimeWrapper */
        public $createdAt;

        /** @var \Eaze\Core\DateTimeWrapper */
        public $photoDate;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;

        # user defined code goes below

    }
