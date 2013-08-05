<?php
    /**
     * AlbumHelper
     * @package    EazyPhoto
     * @subpackage Albums
     * @author     Sergeyfast
     */
    class AlbumHelper {

        /**
         * Get Date String (startDate or startDate - endDate )
         * @param Album $album
         * @return string
         */
        public static function GetDate( Album $album ) {
            if ( !$album->endDate || $album->startDate->DefaultDateFormat() == $album->endDate->DefaultDateFormat() ) {
                return DateTimeHelper::GetRelativeDateString( $album->startDate, false );
            }

            return sprintf( '%s &ndash; %s', DateTimeHelper::GetRelativeDateString( $album->startDate, false ), DateTimeHelper::GetRelativeDateString( $album->endDate, false ) );
        }
    }

?>