<?php
    use Eaze\Core\Response;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\ObjectInfo;


    /**
     * MetaDetail trait
     * @package    Base
     * @subpackage Common
     * @property object        originalObject
     * @property object        currentObject
     * @property string        action
     * @author     sergeyfast
     */
    trait TMetaDetail {

        /**
         * @var MetaDetail
         */
        private $metaDetail;


        /**
         * Before Action
         *
         * if method already exists, use another import construction
         *  use TMetaDetail {
         *      TMetaDetail::beforeAction as mBeforeAction;
         *  }
         *  and call it $this->mBeforeAction()
         */
        protected function beforeAction() {
            if ( $this->originalObject ) {
                $this->metaDetail = MetaDetailUtility::GetByObject( $this->originalObject );
            }
        }


        /**
         * Set Meta Details To Response
         *  use TMetaDetail {
         *      TMetaDetail::beforeSave as mBeforeSave;
         *  }
         *  and call it $this->mBeforeSave()
         */
        protected function beforeSave() {
            Response::setParameter( 'metaDetail', $this->metaDetail );
        }


        /**
         * Get MetaDetail from Request
         * Call at SaveAction->GetFromRequest()
         */
        protected function getMetaDetailFromRequest() {
            $this->metaDetail = MetaDetailFactory::GetFromRequest();
        }


        /**
         * Validate MetaDetail
         * Call at SaveAction->Validate()
         * @param $errors
         */
        protected function validateMetaDetail( &$errors ) {
            $result = MetaDetailFactory::Validate( $this->metaDetail );
            if ( $result ) {
                $errors = array_merge_recursive( $result, $errors );
            }
        }


        /**
         * Save Object History
         * Call at SaveAction->add() and update()
         * @param $object
         * @return bool
         */
        protected function saveMetaDetail( $object ) {
            $metaDetail = $this->metaDetail;
            $result     = false;
            // empty object
            if ( !$metaDetail->pageTitle && !$metaDetail->metaKeywords && !$metaDetail->metaDescription && !$metaDetail->alt && !$metaDetail->canonicalUrl ) {
                if ( $metaDetail->metaDetailId ) {
                    $metaDetail->statusId = StatusUtility::Deleted;

                    $result = MetaDetailFactory::UpdateByMask( $metaDetail, [ 'statusId' ], [ 'metaDetailId' => $metaDetail->metaDetailId ] );
                    if ( $result ) {
                        $metaDetail->metaDetailId = null;
                    }
                } else {
                    $result = true;
                }

                return $result;
            }

            $oi = ObjectInfo::Get( $object );
            if ( !$oi ) {
                return $result;
            }

            $metaDetail->objectId    = $oi->Id;
            $metaDetail->objectClass = $oi->Class;

            if ( $metaDetail->metaDetailId ) {
                $result = MetaDetailFactory::Update( $metaDetail );
            } else {
                $result = MetaDetailFactory::Add( $metaDetail, [ BaseFactory::WithReturningKeys => true ] );
            }

            return $result;

        }
    }