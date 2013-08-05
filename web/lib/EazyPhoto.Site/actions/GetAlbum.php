<?php
    /**
     * GetAlbum Action
     * @package    EazyPhoto
     * @subpackage Site
     * @author     Sergeyfast
     */
    class GetAlbum {

        /**
         * Entry Point
         */
        public function Execute() {
            $key   = Request::getString( 'key' );
            $year  = Page::$RequestData[1];
            $alias = Page::$RequestData[2];

            $search = array(
                'alias'     => $alias
                , 'geStartDate' => Convert::ToDate( '01.01.' . $year )
                , 'leStartDate' => Convert::ToDate( '31.12.' . $year )
            );

            $album = AlbumFactory::GetOne( $search );
            if ( !$album ) {
                Response::HttpStatusCode( 404 );
            }

            if ( $album->isPrivate && $album->folderPath != $key ) {
                Response::HttpStatusCode( 403 );
            }

            $photos = PhotoFactory::Get( array( 'albumId' => $album->albumId )
                , array( BaseFactory::WithoutPages => true, BaseFactory::OrderBy => 'ISNULL(`orderNumber`), `orderNumber`, `photoDate` ' . ( $album->isDescSort ? 'DESC' : 'ASC' ) )
            );

            Response::setParameter( 'album', $album );
            Response::setArray( 'photos', $photos );
        }
    }

?>