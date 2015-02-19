<?php
    use Eaze\Modules\LocaleLoader;

    /**
     * Status Utility
     */
    class StatusUtility {

        const Enabled = 1,
            Disabled = 2,
            Deleted = 3,
            InQueue = 4;

        /**
         * Common Statuses
         *
         * @var array
         */
        public static $Common = [
            LocaleLoader::En => [
                self::Enabled  => 'Enabled',
                self::Disabled => 'Disabled',
            ],
            LocaleLoader::Ru => [
                self::Enabled  => 'Опубликован',
                self::Disabled => 'Не опубликован',
                self::InQueue => 'Новый',
            ]
        ];


        /**
         * Get Status Template
         *
         * @param int $statusId
         * @return string
         */
        public static function GetStatusTemplate( $statusId ) {
            $status = self::$Common[LocaleLoader::$CurrentLanguage][$statusId];

            switch ( $statusId ) {
                case 1:
                    return sprintf( '<span class="status" title="%s">%1$s</span>', $status );
                case 5:
                    return sprintf( '<span class="status _b" title="%s">%1$s</span>', $status );
                default:
                    return sprintf( '<span class="status _fade" title="%s">%1$s</span>', $status );
            }
        }


        /**
         * Get Bool Template
         *
         * @param $bool bool  The bool Value
         * @return string
         */
        public static function GetBoolTemplate( $bool = false ) {
            if ( $bool === null ) {
                return '';
            }

            if ( $bool ) {
                return sprintf( '<span class="status" title="%s">%1$s</span>', 'Да' );
            }

            return sprintf( '<span class="status _fade" title="%s">%1$s</span>', "Нет" );
        }
    }
