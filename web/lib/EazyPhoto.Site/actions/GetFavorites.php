<?php
    use Eaze\Core\Response;
    use Eaze\Model\BaseFactory;

    class GetFavorites {

        /**
         * Entry Point
         */
        public function Execute() {
            // TODO Merge with GetMainPage
            $tags       = TagFactory::Get( [ 'nullParentTagId' => true, 'nnOrderNumber' => true ] );
            $mainAlbums = AlbumFactory::Get( [ 'nnOrderNumber' => true, 'isPrivate' => false ] );
            $map        = TagUtility::GetAllTags();

            $photos = PhotoFactory::Get( [ 'isFavorite' => true ], [ BaseFactory::WithoutPages => true, BaseFactory::OrderBy => '"photoDate" DESC' ] );

            /** fill tags & photos */
            AlbumUtility::FillFirstPhoto( $mainAlbums );
            foreach ( $mainAlbums as $a ) {
                AlbumUtility::FillTags( $map, $a );
            }


            Context::$ActiveSection = Context::Favorites;
            Context::AddBreadcrumbT( 'favorites', LinkUtility::GetFavoritesUrl() );

            Response::SetArray( 'photos', $photos );
            Response::SetArray( 'mainAlbums', $mainAlbums );
            Response::SetArray( 'tags', $tags );
        }
    }