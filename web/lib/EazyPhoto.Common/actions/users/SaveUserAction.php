<?php

    use Eaze\Core\Request;
    use Eaze\Model\BaseFactory;
    use Eaze\Core\Response;

    /**
     * Save User List Action
     *
     * @package    EazyPhoto
     * @subpackage Common
     * @property User        originalObject
     * @property User        currentObject
     * @property UserFactory factory
     */
    class SaveUserAction extends Eaze\Model\BaseSaveAction {

        /**
         * Password
         * @var string
         */
        private $password;


        /**
         * Constructor
         */
        public function __construct() {
            $this->options = [
                BaseFactory::WithReturningKeys => true,
                BaseFactory::WithoutDisabled   => false,
                BaseFactory::WithLists         => true,
            ];

            parent::$factory = new UserFactory();
        }


        /**
         * Form Object From Request
         *
         * @param User $originalObject
         * @return User
         */
        protected function getFromRequest( $originalObject = null ) {
            /** @var User $object */
            $object = parent::$factory->GetFromRequest();

            if ( $originalObject != null ) {
                $object->userId   = $originalObject->userId;
                $object->password = $originalObject->password;
                $object->authKey  = $originalObject->authKey;

                if ( !$object->statusId ) {
                    $object->statusId = $originalObject->statusId;
                }
            }

            if ( $this->password ) {
                $object->password = AuthUtility::EncodePassword( $this->password, 'salt' );
                $object->authKey  = null;
            }

            return $object;
        }


        /**
         * Validate Object
         *
         * @param User $object
         * @return array
         */
        protected function validate( $object ) {
            $errors = parent::$factory->Validate( $object );

            if ( $object->login ) {
                $userId  = $this->originalObject && $this->originalObject->userId ? $this->originalObject->userId : -1;
                $objects = parent::$factory->Get( [ 'login' => $object->login, "!userId" => $userId ], [ BaseFactory::WithoutPages => true ] );
                if ( $objects ) {
                    $errors['fields']['login']['unique'] = 'unique';
                }
            }

            return $errors;
        }


        /**
         * Add Object
         *
         * @param User $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object, $this->options );

            return $result;
        }


        /**
         * Update Object
         *
         * @param User $object
         * @return bool
         */
        protected function update( $object ) {
            $result = parent::$factory->Update( $object );

            return $result;
        }


        protected function beforeAction() {
            $this->password = Request::getParameter( 'password' );
            Response::setParameter( 'password', $this->password );
        }
    }
