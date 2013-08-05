<?php
    /**
     * WTF MFD EG 1.6 [t:trunk]
     * Copyright (c) The 1ADW. All rights reserved.
     */

    /**
     * VfsFolder
     *
     * @package PandaTrunk
     * @subpackage Common
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

        /** @var DateTimeWrapper */
        public $createdAt;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;
    }
?>