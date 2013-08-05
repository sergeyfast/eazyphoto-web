<?php 
    class GetStaticPage {
        
        /**
         * Execute GetStaticPage
         */
        public function Execute() {
        	$with404 = Request::getBoolean( "gsp_With404" );
        	$page    = StaticPageFactory::GetOne( array("url" => Page::$RequestData[0] ) );

        	if ( empty( $page ) && $with404 ) {
        		Response::HttpStatusCode( 404 );
        	}

            if ( !empty( $page ) && empty( $page->content ) ) {
                $map = StaticPageFactory::Get( array("parentStaticPageId" => $page->staticPageId ),
                    array( BaseFactory::WithoutPages => true )
                );

                Response::setParameter( "map", $map );
            }

        	Response::setParameter( "__page", $page );
        }
    }
?>