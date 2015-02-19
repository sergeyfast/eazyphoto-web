<?php


    /**
     * DaemonLock
     *
     * @package EazyPhoto
     * @subpackage Common
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

        /** @var \Eaze\Core\DateTimeWrapper */
        public $runAt;

        /** @var \Eaze\Core\DateTimeWrapper */
        public $maxExecutionTime;

        /** @var bool */
        public $isActive;

        # user defined code goes below

    }
