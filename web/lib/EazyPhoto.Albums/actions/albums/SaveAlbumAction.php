<?php

    use Eaze\Core\DateTimeWrapper;
    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Save Album List Action
     *
     * @package    EazyPhoto
     * @subpackage Albums
     * @property Album        originalObject
     * @property Album        currentObject
     * @property AlbumFactory factory
     */
    class SaveAlbumAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new AlbumFactory();
        }


        /**
         * Form Object From Request
         *
         * @param Album $originalObject
         * @return Album
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var Album $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject !== null ) {
                $object->albumId    = $originalObject->albumId;
                $object->createdAt  = $originalObject->createdAt;
                $object->folderPath = $originalObject->folderPath;
                $object->metaInfo   = $originalObject->metaInfo;
            } else {
                $object->folderPath = AuthUtility::GeneratePassword( 16, true, false, false );
                $object->metaInfo   = [ ];

                if ( !$this->action ) {
                    $object->deleteOriginalsAfter = AlbumUtility::DefaultDeleteDaysInterval;
                }
            }

            $object->modifiedAt = DateTimeWrapper::Now();

            return $object;
        }


        /**
         * Validate Object
         *
         * @param Album $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param Album $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            EazyPhotoDaemon::QueueAlbums();

            return $result;
        }


        /**
         * Update Object
         *
         * @param Album $object
         * @return bool
         */
        protected function update( $object ) {
            if ( EazyPhotoDaemon::Enabled() ) {
                $result = parent::$factory->Update( $object );
                EazyPhotoDaemon::UpdateMeta( $object->albumId );
            } else {
                AlbumUtility::FillMetaInfo( $object );
                $result = parent::$factory->Update( $object );
            }


            return $result;
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $users = UserFactory::Get( [ ], [ BaseFactory::WithoutPages => true ] );
            Response::setArray( 'users', $users );
        }
    }
