<?php
    /**
     * Static Page Utility
     */
    class StaticPageUtility {

        /**
         * Get All Static Pages (staticPageId, title, parentStaticPageId )
         * 
         * @return array
         */
        public static function GetData(){
            $conn = ConnectionFactory::Get();
            $columns  = array( "staticPageId", "title", "parentStaticPageId");
            for ( $i = 0; $i < count( $columns ); $i ++ ) {
                $columns[$i] = $conn->quote( $columns[$i] );
            }
            
            return StaticPageFactory::Get( null,
                array( BaseFactory::WithoutPages => true, BaseFactory::WithColumns => implode( ",", $columns ))
            );
        }


        /**
         * Get Collapsed Static Pages (staticPageId, title, parentStaticPageId )
         * @return array collapsed static pages
         */
        public static function GetCollapsedData() {
            return self::Collapse(self::GetData());
        }


        /**
         * Collapse Static Pages to Nodes
         * @param  $pages
         * @return array
         */
        public static function Collapse( $pages ) {
            $tree = array();
            foreach ( $pages as $page ) {
                $id  = $page->staticPageId;
                $pid = $page->parentStaticPageId;
                if ( is_null($page->nodes) ) {
                    $page->nodes = array();
                }

                if ( empty( $pid ) ) {
                    $tree[$id] = $page;
                } else {
                    $pages[$pid]->nodes[$id] = $page;
                }
            }
            return $tree;
        }
    }
?>