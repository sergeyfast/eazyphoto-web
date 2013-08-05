<?php
    /**
     * WTF MFD EG 1.6 [t:trunk]
     * Copyright (c) The 1ADW. All rights reserved.
     */

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

        /** @var DateTimeWrapper */
        public $createdAt;

        /** @var DateTimeWrapper */
        public $photoDate;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;
    }
?>