<?php
    use Eaze\Core\Response;

    /**
     * Get Navigations
     */
    class GetContext {

        /**
         * Execute
         */
        public function Execute() {
            Context::LoadMainNavigations();
            Context::DetectNavigation();
            Context::DetectMeta();

            $sph = SiteParamHelper::GetInstance();

            Response::SetParameter( 'sph', $sph );
        }
    }
