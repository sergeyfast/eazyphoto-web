<?php
    /**
     * Get Navigations
     */
    class GetNavigations {

        /**
         * Navigations
         * @var array
         */
        private $navigations = array();


        /**
         * Get Header Menu
         * @return array
         */
        private function getFooterMenu() {
            $result = NavigationUtility::GetByAlias( $this->navigations, NavigationUtility::FooterMenu );

            return $result;
        }


        /**
         * Execute
         */
        public function Execute() {
            $this->navigations = NavigationFactory::Get( array(), array( BaseFactory::WithoutPages => true ) );

            Response::setParameter( '__footerMenu', $this->getFooterMenu() );
        }
    }

?>