<?php
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
            $year   = ArrayHelper::GetValue( Page::$RequestData, 1 );
            $search = array(
                'isPrivate' => $this->isLogged() ? null: false
                , 'geStartDate' => $year ? Convert::ToDate( '01.01.' . $year ) : null
                , 'leStartDate'   => $year ? Convert::ToDate( '31.12.' . $year ) : null
            );

            $albums = AlbumFactory::Get( $search, array( BaseFactory::WithoutPages => true ) );
            $photos = $albums ? $this->getPhotos( $albums ) : null;

            Response::setArray( 'albums', $albums );
            Response::setArray( 'photos', $photos );
            Response::setInteger( 'year', $year );
        }


        /**
         * @param Album[] $albums
         * @return array
         */
        private function getPhotos( $albums ) {
            $photoIds[] = array();
            $result     = array();

            foreach( $albums as $a ) {
                if ( $a->metaInfo && !empty( $a->metaInfo['photoIds'] ) ) {
                    foreach( $a->metaInfo['photoIds'] as $id ) {
                        $photoIds[] = $id;
                    }
                }
            }


            if( $photoIds ) {
                $result = PhotoFactory::Get( array( '_photoId' => $photoIds ), array( BaseFactory::WithoutPages => true ) );
            }

            return $result;
        }
    }

?>