<?php
    use Eaze\Core\Convert;
    use Eaze\Core\Request;
    use Eaze\Core\Response;
    use Eaze\Model\BaseFactory;
    use Eaze\Site\Page;

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

            $search = [
                'alias'     => $alias
                , 'geStartDate' => Convert::ToDate( '01.01.' . $year )
                , 'leStartDate' => Convert::ToDate( '31.12.' . $year )
            ];

            $album = AlbumFactory::GetOne( $search );
            if ( !$album ) {
                Response::HttpStatusCode( 404 );
            }

            if ( $album->isPrivate && $album->folderPath !== $key ) {
                Response::HttpStatusCode( 403 );
            }

            $photos = PhotoFactory::Get( [ 'albumId' => $album->albumId ]
                , [ BaseFactory::WithoutPages => true, BaseFactory::OrderBy => '"orderNumber", "photoDate" ' . ( $album->isDescSort ? 'DESC' : 'ASC' ) ]
            );

            Response::setParameter( 'album', $album );
            Response::setArray( 'photos', $photos );
        }
    }