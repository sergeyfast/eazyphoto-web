<?php
    use Eaze\Core\Response;

    class GetMainPage {

        /**
         * Entry Point
         */
        public function Execute() {
            $tags       = TagFactory::Get( [ 'nullParentTagId' => true, 'nnOrderNumber' => true ] );
            $mainAlbums = AlbumFactory::Get( [ 'nnOrderNumber' => true, 'isPrivate' => false ] );
            $map        = TagUtility::GetAllTags();

            /** first tag logic */
            $albums = [ ];
            $tag    = $tags && $map ? current( $tags ) : null;
            if ( $tag ) {
                $tagIds = array_keys( TagUtility::FilterTags( $map, $tag->tagId ) );
                $albums = AlbumFactory::Get( [ 'pageSize' => 12 ], [ \Eaze\Model\BaseFactory::CustomSql => AlbumUtility::GetWithTagIdSql( $tagIds ) ] );
            }

            /** fill tags & photos */
            AlbumUtility::FillFirstPhoto( $mainAlbums, $albums );
            foreach ( $mainAlbums as $a ) {
                AlbumUtility::FillTags( $map, $a );
            }

            Response::SetParameter( 'tag', $tag );
            Response::SetArray( 'tags', $tags );
            Response::SetArray( 'albums', $albums );
            Response::SetArray( 'mainAlbums', $mainAlbums );
        }
    }
