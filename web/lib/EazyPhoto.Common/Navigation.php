<?php
    /**
     * Navigation
     *
     * @package Panda
     * @subpackage Common
     */
    class Navigation {

        /** @var int */
        public $navigationId;

        /** @var string */
        public $title;

        /** @var int */
        public $orderNumber;

        /** @var int */
        public $navigationTypeId;

        /** @var NavigationType */
        public $navigationType;

        /** @var int */
        public $staticPageId;

        /** @var StaticPage */
        public $staticPage;

        /** @var string */
        public $url;

        /** @var int */
        public $statusId;

        /** @var Status */
        public $status = null;

        public function getLink() {
            if (!empty( $this->staticPageId ) && !empty( $this->staticPage->url ) ) {
                return Site::GetWebPath($this->staticPage->url);
            } else  if (!empty( $this->url)) {
                if ( mb_strpos($this->url, 'http') === 0 ) {
                    return $this->url;
                } else {
                    return Site::GetWebPath($this->url);
                }
            } else {
                return '/404';
            }
        }
    }
?>