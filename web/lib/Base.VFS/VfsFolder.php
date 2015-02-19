<?php


    /**
     * VfsFolder
     *
     * @package Base
     * @subpackage VFS
     */
    class VfsFolder extends BaseTreeObject {

        /** @var int */
        public $folderId;

        /** @var int */
        public $parentFolderId;

        /** @var VfsFolder */
        public $parentFolder;

        /** @var string */
        public $title;

        /** @var bool */
        public $isFavorite;

        /** @var \Eaze\Core\DateTimeWrapper */
        public $createdAt;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;

        # user defined code goes below

    }
