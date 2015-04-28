<?php

    use Eaze\Core\DateTimeWrapper;
    use Eaze\Database\ConnectionFactory;
    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Save Photo List Action
     *
     * @package    EazyPhoto
     * @subpackage Albums
     * @property Photo        originalObject
     * @property Photo        currentObject
     * @property PhotoFactory factory
     */
    class SavePhotoAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new PhotoFactory();
        }


        /**
         * Form Object From Request
         *
         * @param Photo $originalObject
         * @return Photo
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var Photo $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->photoId = $originalObject->photoId;
                $object->exif    = $originalObject->exif;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param Photo $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param Photo $object
         * @return bool
         */
        protected function add( $object ) {
            ConnectionFactory::BeginTransaction();

            $result = parent::$factory->Add( $object, $this->options );
            $result = $result && $this->updateAlbum( $object->albumId );

            ConnectionFactory::CommitTransaction( $result );

            return $result;
        }


        /**
         * Update ALbum
         * @param $albumId
         * @return array|bool
         */
        private function updateAlbum( $albumId ) {
            if ( !EazyPhotoDaemon::Enabled() ) {
                $album = AlbumFactory::GetById( $albumId );
                if ( $album ) {
                    $album->modifiedAt = DateTimeWrapper::Now();
                    AlbumUtility::FillMetaInfo( $album );
                    return AlbumFactory::Update( $album );
                }
            } else {
                return EazyPhotoDaemon::UpdateMeta( $albumId );
            }

            return false;
        }


        protected function delete( $object ) {
            ConnectionFactory::BeginTransaction();

            $result = parent::$factory->Delete( $object );
            $result = $result && $this->updateAlbum( $object->albumId );

            ConnectionFactory::CommitTransaction( $result );

            return $result;
        }


        /**
         * Update Object
         *
         * @param Photo $object
         * @return bool
         */
        protected function update( $object ) {
            ConnectionFactory::BeginTransaction();

            $result = parent::$factory->Update( $object );
            $result = $result && $this->updateAlbum( $object->albumId );

            ConnectionFactory::CommitTransaction( $result );

            return $result;
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $albums = AlbumFactory::Get( [ 'statusId' => StatusUtility::Enabled ], [ BaseFactory::WithoutPages => true, BaseFactory::WithColumns => ' "albumId", "title" ' ] );
            Response::SetArray( 'albums', $albums );
        }
    }
