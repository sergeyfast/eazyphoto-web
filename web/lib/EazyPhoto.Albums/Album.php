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

        /**
         * @var Photo
         */
        public $Photo;

        /**
         * @var Tag[]
         */
        public $Tags = [];

        /**
         * @var Tag[]
         */
        public $AllTags = [];

        /**
         * Get Main Photo Id
         * @return mixed
         */
        public function PhotoId() {
            if ( $this->metaInfo && !empty( $this->metaInfo['photoIds'] ) ) {
                return reset( $this->metaInfo['photoIds'] );
            }

            return null;
        }


        /**
         * Get Count
         * @return int
         */
        public function Count() {
            if ( $this->metaInfo && !empty( $this->metaInfo['count'] ) ) {
                return $this->metaInfo['count'];
            }

            return null;
        }

    }
