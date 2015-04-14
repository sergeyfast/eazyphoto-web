<?php


    /**
     * Navigation
     *
     * @package EazyPhoto
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
        public $status;

        /** @var array */
        public $nodes;

        /** @var array */
        public $params;

        # user defined code goes below

        /**
         * Get Link
         * StaticPage, Url, Behavior
         * @param bool $withWebPath
         * @return string
         */
        public function GetLink( $withWebPath = false ) {
            $url = '#';
            if ( $this->staticPageId && $this->staticPage->url ) {
                $url = $this->staticPage->url;
            } else if ( $this->url ) {
                $url = $this->url;
                if ( mb_strpos( $this->url, 'http' ) === 0 || mb_strpos( $this->url, 'mailto:' ) === 0 ) {
                    $withWebPath = false;
                }
            }

            return $withWebPath ? \Eaze\Site\Site::GetWebPath( $url ) : $url;
        }


        /**
         * Image
         * @return string
         */
        public function Image() {
            $this->params = $this->params ?: [ ];
            return \Eaze\Helpers\ArrayHelper::GetValue( $this->params, 'image' );
        }

    }
