<?php
    use Eaze\Core\Response;

    class GetMainPage {

        /**
         * Entry Point
         */
        public function Execute() {
            $tags        = TagFactory::Get( [ 'nullParentTagId' => true, 'nnOrderNumber' => true ] );
            $mainAlbums  = AlbumFactory::Get( [ 'nnOrderNumber' => true, 'isPrivate' => false ] );
            $map         = TagUtility::GetAllTags();
            $albumsByTag = TagUtility::GetAlbumsByTags( $map, $tags );
            $albums      = AlbumUtility::FillAlbums( $albumsByTag );

            /** fill tags & photos */
            AlbumUtility::FillFirstPhoto( $mainAlbums, $albums );
            foreach ( $mainAlbums as $a ) {
                AlbumUtility::FillTags( $map, $a );
            }

            Response::SetArray( 'mainAlbums', $mainAlbums );
            Response::SetArray( 'albumsByTag', $albumsByTag );
            Response::SetArray( 'tags', $tags );
        }
    }
