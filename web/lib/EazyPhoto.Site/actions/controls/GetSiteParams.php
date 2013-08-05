<?php
    class GetSiteParams {

        /**
         * Execute GetSiteParams
         */
        public function Execute() {
            $container = Request::getString( 'gsp_Container' );
            $instance  = Request::getString( 'gsp_Instance' );

            Response::setArray( $container,    SiteParamHelper::GetSiteParams() );
            Response::setParameter( $instance, SiteParamHelper::GetInstance() );
        }
    }

?>