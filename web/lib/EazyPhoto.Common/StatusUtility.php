<?php
    /**
     * Status Utility
     *
     */
    class StatusUtility {

        const
            Enabled = 1,
            Disabled = 2,
            InQueue = 4;

        /**
         * Common Statuses
         *
         * @var array
         */
        public static $Common = array(
            'en' => array(
                1   => 'Enabled'
                , 2 => 'Disabled'
            )
            , 'ru' => array(
                1   => 'Опубликован'
                , 2 => 'Не опубликован'
			)
        );
        
        /**
         * Album Statuses
         *
         * @var array
         */
        public static $Album = array(
            'en' => array(
                1   => 'Enabled'
                , 2 => 'Disabled'
                , 4 => 'In Queue'
            )
            , 'ru' => array(
                4   => 'Новый'
                , 1 => 'Опубликован'
                , 2 => 'Не опубликован'
			)
        );

        /**
         * Get Status Template
         *
         * @param int $statusId
         * @return string
         */
        public static function GetStatusTemplate( $statusId ) {
            $status = self::$Album[LocaleLoader::$CurrentLanguage][$statusId];

            switch ($statusId) {
            	case 1:
            	    return sprintf( '<span class="status green" title="%s">%s</span>', $status, $status);
                case 4:
            	    return sprintf( '<span class="status blue" title="%s">%s</span>', $status, $status);
            	default:
            	    return sprintf( '<span class="status" title="%s">%s</span>', $status, $status);
            }
        }


        /**
         * Get Bool Template
         *
         * @param $bool bool  The bool Value
         * @return string
         */
        public static function GetBoolTemplate( $bool = false ) {
            if ( $bool ) {
                return sprintf( '<span class="status green" title="%s">%s</span>', "Да", "Да");
            } else {
                return sprintf( '<span class="status" title="%s">%s</span>', "Нет", "Нет");
            }
        }        
    }
?>