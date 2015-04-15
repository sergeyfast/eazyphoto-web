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
         * Entry Point
         */
        public function Execute() {
            $page   = Request::getInteger( 'page' );
            $count  = Request::getInteger( 'ga_Count' );
            $search = [ 'pageSize' => $count ];
            $as     = AlbumSearch::GetFromRequest();
            $search += $as->GetSearch();
            $options = [ BaseFactory::CustomSql => $as->GetCustomSql(), BaseFactory::OrderBy => $as->GetOrderBySql() ];

            $objectsCount   = AlbumFactory::Count( $search, [ BaseFactory::CustomSql => $options[BaseFactory::CustomSql], BaseFactory::WithoutPages => true ] );
            $pagesCount     = ceil( $objectsCount / $count );
            $page           = ( ( $page + 1 > $pagesCount || $page < 0 ) && $objectsCount > 0 ) ? 0 : $page;
            $search['page'] = $page;

            $list   = AlbumFactory::Get( $search, $options );
            $photos = $list ? $this->getPhotos( $list ) : null;

            AlbumUtility::FillFirstPhoto( $list );
            foreach ( $list as $a ) {
                AlbumUtility::FillTags( $as->TagMap, $a );
            }

            Context::$ActiveSection = Context::Albums;
            Context::AddBreadcrumbT( 'albums', LinkUtility::GetAlbumsUrl() );

            Response::setParameter( 'as', $as );
            Response::setArray( 'albums', $list );
            Response::setArray( 'photos', $photos );
            Response::setInteger( '__pageNumber', $page );
            Response::setInteger( '__pageCount', $pagesCount );
            Response::setInteger( '__objectsCount', $objectsCount );
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