<?php


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

        /** @var \Eaze\Core\DateTimeWrapper */
        public $startDate;

        /** @var \Eaze\Core\DateTimeWrapper */
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

        /** @var \Eaze\Core\DateTimeWrapper */
        public $createdAt;

        /** @var \Eaze\Core\DateTimeWrapper */
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

        /** @var array */
        public $tagIds;

        # user defined code goes below

    }
