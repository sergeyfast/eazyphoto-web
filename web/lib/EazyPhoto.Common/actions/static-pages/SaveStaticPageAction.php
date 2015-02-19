<?php

    use Eaze\Database\ConnectionFactory;
    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;
    use Eaze\Model\ObjectInfo;

    /**
     * Save StaticPage List Action
     *
     * @package    EazyPhoto
     * @subpackage Common
     * @property StaticPage        originalObject
     * @property StaticPage        currentObject
     * @property StaticPageFactory factory
     */
    class SaveStaticPageAction extends Eaze\Model\BaseSaveAction {

        use TMetaDetail {
            TMetaDetail::beforeSave as mBeforeSave;
        }

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new StaticPageFactory();
            ObjectImageUtility::InitializeMappings( parent::$factory );
        }


        /**
         * Form Object From Request
         *
         * @param StaticPage $originalObject
         * @return StaticPage
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var StaticPage $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->staticPageId = $originalObject->staticPageId;
            }

            $this->getMetaDetailFromRequest();

            return $object;
        }


        /**
         * Validate Object
         *
         * @param StaticPage $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            $imageErrors = ObjectImageUtility::ValidateImages( $object->images );
            if ( !empty( $imageErrors ) ) {
                $errors['fields']['images']['format'] = 'format';
                Response::setParameter( 'imageErrors', $imageErrors );
            }

            if ( $object->url ) { // url unique
                if ( !$this->originalObject ) {
                    $this->originalObject               = new StaticPage();
                    $this->originalObject->staticPageId = -1;
                }

                $objects = parent::$factory->Get( [ 'url' => $object->url, '!staticPageId' => $this->originalObject->staticPageId ], [ BaseFactory::WithoutPages => true ] );
                if ( $objects ) {
                    $errors['fields']['url']['unique'] = 'unique';
                }
            }


            $this->validateMetaDetail( $errors );

            return $errors;
        }


        /**
         * Add Object
         *
         * @param StaticPage $object
         * @return bool
         */
        protected function add( $object ) {
            ConnectionFactory::BeginTransaction();

            $result = parent::$factory->Add( $object, $this->options );
            $result = $result && ObjectImageUtility::SaveImages( $object, $this->originalObject );
            $result = $result && $this->saveMetaDetail( $object );

            ConnectionFactory::CommitTransaction( $result );

            return $result;
        }


        /**
         * Update Object
         *
         * @param StaticPage $object
         * @return bool
         */
        protected function update( $object ) {
            ConnectionFactory::BeginTransaction();

            $result = parent::$factory->Update( $object );
            $result = $result && ObjectImageUtility::SaveImages( $object, $this->originalObject );
            $result = $result && $this->saveMetaDetail( $object );

            ConnectionFactory::CommitTransaction( $result );

            return $result;
        }


        /**
         * @param StaticPage $object
         */
        protected function refillObject( $object ) {
            $oi             = ObjectInfo::Get( $object );
            $object->images = ObjectImageFactory::Get( [ 'objectId' => $oi->Id, 'objectClass' => $oi->Class ], [ BaseFactory::WithoutPages => true ] );
        }


        /**
         * Set Json Object Images to Template
         * @return void
         */
        protected function beforeSave() {
            if ( $this->action != self::UpdateAction && !empty( $this->currentObject->staticPageId ) ) {
                $this->refillObject( $this->currentObject );
            }

            Response::setParameter( 'imageData', ObjectImageUtility::PrepareImagesData( $this->currentObject ) );

            $this->mBeforeSave();
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $staticPages = StaticPageFactory::Get( [ ], [ BaseFactory::WithoutPages => true ] );
            Response::setArray( 'staticPages', $staticPages );
        }
    }