<?php
    /**
     * Sync Photos Daemon
     * @package    EazyPhoto
     * @subpackage Site
     * @author     Sergeyfast
     */
    class SyncPhotosDaemon {

        /**
         * Entry Point
         */
        public function Execute() {
            $rules = Request::getParameter( 'rules' );
            if ( $rules ) {
                DaemonUtility::Run( $rules );
            }
        }

        /**
         * Run
         */
        public static function Run() {
            self::stageOne(); // Queue Albums
            self::stageTwo(); // Process Photos
            // TODO Stage3: delete originals
        }


        /**
         * Add Albums to BTSync
         */
        private static function stageOne() {
            $bts = BTSyncUtility::GetClient();
            if ( !$bts->RequestToken() ) {
                Logger::Error( 'Cannot get token from BTSync ' );
            }

            Logger::Debug( 'Adding new albums to queue' );
            $albums = AlbumFactory::Get( array( 'statusId' => StatusUtility::InQueue ), array( BaseFactory::WithoutPages => true ) );
            $result = false;
            if ( $albums ) {
                foreach( $albums as $album ) {
                    $result = AlbumUtility::QueueAlbum( $album, $bts );
                    if ( !$result ) {
                        break;
                    }
                }
            }
        }


        /**
         * Sync Photos
         */
        private static function stageTwo() {
            PhotoFactory::$mapping['view'] = 'photos';
            $albums = AlbumFactory::Get( array(), array( BaseFactory::WithoutPages => true ) );
            foreach( $albums as $album ) {
                Logger::Info( 'Starting syncing album %d', $album->albumId );
                AlbumUtility::SyncPhotos( $album );
            }

        }
    }

?>