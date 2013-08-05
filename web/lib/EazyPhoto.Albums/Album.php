<?php
    /**
     * WTF MFD EG 1.6 [t:trunk]
     * Copyright (c) The 1ADW. All rights reserved.
     */

    /**
     * Album
     *
     * @package EazyPhoto
     * @subpackage Albums
     */
    class Album {

        /** @var int */
        public $albumId;

        /** @var string */
        public $title;

        /** @var string */
        public $description;

        /** @var string */
        public $alias;

        /** @var bool */
        public $isPrivate;

        /** @var DateTimeWrapper */
        public $startDate;

        /** @var DateTimeWrapper */
        public $endDate;

        /** @var int */
        public $orderNumber;

        /** @var string */
        public $folderPath;

        /** @var string */
        public $roSecret;

        /** @var string */
        public $roSecretHd;

        /** @var int */
        public $deleteOriginalsAfter;

        /** @var bool */
        public $isDescSort;

        /** @var DateTimeWrapper */
        public $createdAt;

        /** @var DateTimeWrapper */
        public $modifiedAt;

        /** @var int */
        public $userId;

        /** @var User */
        public $user;

        /** @var array */
        public $metaInfo;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;
    }
?>