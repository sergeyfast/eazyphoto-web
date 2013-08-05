<?php
    /**
     * WTF MFD EG 1.6 [t:trunk]
     * Copyright (c) The 1ADW. All rights reserved.
     */

    /**
     * DaemonLock
     */
    class DaemonLock {

        /** @var int */
        public $daemonLockId;

        /** @var string */
        public $title;

        /** @var string */
        public $packageName;

        /** @var string */
        public $methodName;

        /** @var DateTimeWrapper */
        public $runAt;

        /** @var DateTimeWrapper */
        public $maxExecutionTime;

        /** @var bool */
        public $isActive;
    }
?>