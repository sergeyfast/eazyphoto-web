<?php

    use Eaze\Site\Site;

    /**
     * LinkUtility
     * @package    EazyPhoto
     * @subpackage Site
     * @author     Sergeyfast
     */
    class LinkUtility {

        /**
         * Get Base Url
         * @static
         * @param      $url
         * @param bool $withWebPath use Site::GetWebPath
         * @internal param string $baseUrl
         * @return string
         */
        private static function getBaseUrl( $url, $withWebPath = false ) {
            return $withWebPath ? Site::GetWebPath( $url ) : $url;
        }


        public static function GetAlbumPath( Album $album, $folderType, $withWebPath = false ) {
            return self::getBaseUrl( 'albums://', $withWebPath ) . sprintf( '%d/%s/%s/', $album->startDate->format( 'Y' ), $album->folderPath, $folderType );
        }


        public static function GetPhotoThumb( Photo $photo, $withWebPath = false ) {
            return self::GetAlbumPath( $photo->album, AlbumUtility::Thumbs, $withWebPath ) . $photo->filename;
        }


        public static function GetPhotoHd( Photo $photo, $withWebPath = false ) {
            return self::GetAlbumPath( $photo->album, AlbumUtility::HD, $withWebPath ) . $photo->filename;
        }


        public static function GetAlbumUrl( Album $album, $withWebPath = false ) {
            $private = $album->isPrivate ? sprintf( '?key=%s', $album->folderPath ) : '';
            return self::getBaseUrl( '/', $withWebPath ) . sprintf( '%s/%s/%s', $album->startDate->format( 'Y' ), $album->alias, $private );
        }


        public static function GetAlbumsUrl( $withWebPath = false ) {
            return self::getBaseUrl( Context::Albums, $withWebPath );
        }


        public static function GetAlbumsYearUrl( $year = null, $withWebPath = false ) {
            return self::getBaseUrl( '/', $withWebPath ) . $year . '/';
        }


        /**
         * @param Tag  $tag
         * @param bool $withWebPath
         * @return string
         */
        public static function GetTagUrl( $tag, $withWebPath = false ) {
            return self::getBaseUrl( '/albums/', $withWebPath ) . '?tag=' . $tag->alias;
        }
    }