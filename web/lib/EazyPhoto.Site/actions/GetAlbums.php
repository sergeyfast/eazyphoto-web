<?php
    use Eaze\Core\Convert;
    use Eaze\Core\Request;
    use Eaze\Core\Response;
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Model\BaseFactory;
    use Eaze\Site\Page;

    /**
     * GetAlbums Action
     * @package    EazyPhoto
     * @subpackage Site
     * @author     Sergeyfast
     */
    class GetAlbums {

        /**
         * Is Logged
         * @return object
         */
        private function isLogged() {
            $user = AuthUtility::GetCurrentUser( 'User' );
            return ( $user );
        }


        /**
         * Entry Point
         */
        public function Execute() {
            $year     = ArrayHelper::GetValue( Page::$RequestData, 1 );
            $tagAlias = Request::GetString( 'tag' );
            $options  = [ BaseFactory::OrderBy => '"albumId" DESC' ];
            $search   = [
                'isPrivate'   => $this->isLogged() ? null : false,
                'geStartDate' => $year ? Convert::ToDate( '01.01.' . $year ) : null,
                'leStartDate' => $year ? Convert::ToDate( '31.12.' . $year ) : null,
                'page'        => abs( Request::getInteger( 'page' ) ),
                'pageSize'    => 15
            ];

            // tag filter
            $tag = $tagAlias ? TagFactory::GetOne( [ 'alias' => $tagAlias ] ) : null;
            $map = TagUtility::GetAllTags();
            if ( $tag ) {
                $tagIds                          = array_keys( TagUtility::FilterTags( $map, $tag->tagId ) );
                $options[BaseFactory::CustomSql] = AlbumUtility::GetWithTagIdSql( $tagIds );
            }

            $pageCount      = AlbumFactory::Count( $search, $options );
            $search['page'] = $search['page'] > $pageCount ? 0 : $search['page'];
            $albums         = AlbumFactory::Get( $search, $options );
            $photos         = $albums ? $this->getPhotos( $albums ) : null;


            AlbumUtility::FillFirstPhoto( $albums );
            foreach ( $albums as $a ) {
                AlbumUtility::FillTags( $map, $a );
            }

            Context::$ActiveSection = Context::Albums;
            Context::AddBreadcrumbT( 'albums', LinkUtility::GetAlbumsUrl() );

            Response::setArray( 'albums', $albums );
            Response::setArray( 'photos', $photos );
            Response::setArray( 'tagMap', $map );
            Response::setInteger( 'year', $year );
            Response::setParameter( 'tag', $tag );
            Response::setParameter( 'page', $search['page'] );
            Response::setParameter( 'pageCount', $pageCount );
        }


        /**
         * @param Album[] $albums
         * @return array
         */
        private function getPhotos( $albums ) {
            $photoIds = [ ];
            $result   = [ ];

            foreach ( $albums as $a ) {
                if ( $a->metaInfo && !empty( $a->metaInfo['photoIds'] ) ) {
                    foreach ( $a->metaInfo['photoIds'] as $id ) {
                        $photoIds[] = $id;
                    }
                }
            }


            if ( $photoIds ) {
                $result = PhotoFactory::Get( [ '_photoId' => $photoIds ], [ BaseFactory::WithoutPages => true ] );
            }

            return $result;
        }
    }