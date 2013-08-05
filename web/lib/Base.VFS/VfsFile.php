<?php
    /**
     * WTF MFD EG 1.6 [t:trunk]
     * Copyright (c) The 1ADW. All rights reserved.
     */

    /**
     * VfsFile
     *
     * @package Base
     * @subpackage VFS
     */
    class VfsFile {

        /** @var int */
        public $fileId;

        /** @var int */
        public $folderId;

        /** @var VfsFolder */
        public $folder;

        /** @var string */
        public $title;

        /** @var string */
        public $path;

        /** @var array */
        public $params;

        /** @var bool */
        public $isFavorite;

        /** @var string */
        public $mimeType;

        /** @var int */
        public $fileSize;

        /** @var bool */
        public $fileExists;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status;

        /** @var DateTimeWrapper */
        public $createdAt;
    }
?>