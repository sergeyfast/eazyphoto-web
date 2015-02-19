<?php


    /**
     * User
     *
     * @package EazyPhoto
     * @subpackage Common
     */
    class User {

        /** @var int */
        public $userId;

        /** @var string */
        public $login;

        /** @var string */
        public $password;

        /** @var \Eaze\Core\DateTimeWrapper */
        public $lastActivityAt;

        /** @var string */
        public $authKey;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;

        # user defined code goes below

    }
