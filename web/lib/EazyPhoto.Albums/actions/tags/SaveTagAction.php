<?php

    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Save Tag List Action
     *
     * @package EazyPhoto
     * @subpackage Albums
     * @property Tag originalObject
     * @property Tag currentObject
     * @property TagFactory factory
     */
    class SaveTagAction extends Eaze\Model\BaseSaveAction {

        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new TagFactory();
        }


        /**
         * Form Object From Request
         *
         * @param Tag $originalObject
         * @return Tag
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var Tag $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject !== null ) {
                $object->tagId = $originalObject->tagId;
            }

            if ( $object->photoId ) {
                $object->photoPath = LinkUtility::GetPhotoHd( $object->photo, false );
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param Tag $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            if ( $object->parentTagId && $object->parentTag->parentTagId ) {
                $errors['fields']['parentTagId']['format'] = 'format';
            }

            if ( $object->tagId && $object->parentTagId && $object->parentTagId === $object->tagId ) {
                $errors['fields']['parentTagId']['format'] = 'format';
            }

            if ( $object->alias ) { // unique alias
                if ( !$this->originalObject ) {
                    $this->originalObject        = new Tag();
                    $this->originalObject->tagId = -1;
                }

                $objects = parent::$factory->Get( [ 'alias' => $object->alias, '!tagId' => $this->originalObject->tagId ], [ BaseFactory::WithoutPages => true ] );
                if ( $objects ) {
                    $errors['fields']['alias']['unique'] = 'unique';
                }
            }


            if ( $object->title ) { // unique title
                if ( !$this->originalObject ) {
                    $this->originalObject        = new Tag();
                    $this->originalObject->tagId = -1;
                }

                $objects = parent::$factory->Get( [ 'eTitle' => $object->title, '!tagId' => $this->originalObject->tagId ], [ BaseFactory::WithoutPages => true ] );
                if ( $objects ) {
                    $errors['fields']['title']['unique'] = 'unique';
                }
            }

            return $errors;
        }


        /**
         * Add Object
         *
         * @param Tag $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param Tag $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );

            return $result;
        }


        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {
            $tags = TagFactory::Get( [ 'nullParentTagId' => true ], [BaseFactory::WithoutPages => true ] );
            Response::setArray( 'parentTags', $tags );
        }
    }
