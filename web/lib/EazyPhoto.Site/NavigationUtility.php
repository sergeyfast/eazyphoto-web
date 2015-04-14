<?php


    /**
     * Navigation Utility
     */
    class NavigationUtility {

        /**
         * Main Menu
         */
        const MainMenu = 'main-menu';

        /**
         * Footer Menu
         */
        const FooterMenu = 'footer-menu';

        /**
         * Social Menu
         */
        const SocialMenu = 'social-menu';


        /**
         * Get Navigations by alias
         * @param array  $navigations source array
         * @param string $alias       navigation type alias
         * @return array
         */
        public static function GetByAlias( $navigations, $alias ) {
            $result = [ ];
            foreach ( $navigations as $navigation ) {
                if ( $navigation->navigationType->alias === $alias ) {
                    $result[$navigation->navigationId] = $navigation;
                }
            }

            return $result;
        }
    }
