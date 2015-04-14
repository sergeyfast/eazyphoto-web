<?php


    /**
     * Tag Helper
     * @package    EazyPhoto
     * @subpackage Albums
     * @author     sergeyfast
     */
    class TagHelper {


        /**
         * @param $tags
         * @return string
         */
        public static function GetTagLinks( $tags ) {
            if ( !$tags ) {
                return '';
            }

            $result = [ ];
            foreach ( $tags as $t ) {
                $result[] = \Eaze\Helpers\FormHelper::FormLink( LinkUtility::GetTagUrl( $t, true ), $t->title, null, 'tag' );
            }

            return implode( ' ', $result );
        }

    }