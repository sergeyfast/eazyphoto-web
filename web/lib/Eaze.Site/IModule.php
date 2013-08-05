<?php
    /**
     * IModule Interface
     */
    interface IModule {
        /**
         * Init Module
         *
         * @param DOMNodeList $params  the params node list
         * @static 
         */
        public static function Init( DOMNodeList $params );
    }
?>