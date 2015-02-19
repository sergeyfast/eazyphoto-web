<?php
    use Eaze\Core\Request;
    use Eaze\Core\Response;
    use Eaze\Site\Page;

    class GetStaticPage {

        /**
         * Execute GetStaticPage
         */
        public function Execute() {
            $with404 = Request::getBoolean( 'gsp_With404' );
            $url     = Request::getString( 'gsp_Url' );
            $url     = $url ?: Page::$RequestData[0];
            $page    = StaticPageFactory::GetOne( [ 'url' => $url, 'statusId' => 1 ] );

            if ( !$page && $with404 ) {
                Response::HttpStatusCode( 404 );
            }

            if ( $page ) {
                $page->images = ObjectImageFactory::Get( [ 'objectId' => $page->staticPageId, 'objectClass' => get_class( $page ) ] );
            }

            Context::SetObject( $page );

            Response::setParameter( '__page', $page );
        }
    }
