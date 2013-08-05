<?php
    /**
     * Save User Action
     * 
     * @package PandaTrunk
     * @subpackage Common
     */
    class SaveUserAction extends BaseSaveAction  {

        /**
         * Password
         * @var string
         */
        private $password = null;
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->options = array(
                BaseFactory::WithoutDisabled => false
                , BaseFactory::WithLists     => true
            );

            parent::$factory = new UserFactory();
        }

        protected function beforeAction() {
            $this->password = Request::getParameter( 'password' );
            Response::setParameter( 'password', $this->password );
        }
               
        /**
         * Form Object From Request
         *
		 * @param User $originalObject 
         * @return User
         */
        protected function getFromRequest( $originalObject = null ) {
            $object = parent::$factory->GetFromRequest();
            
            if ( $originalObject != null ) {
                $object->userId   = $originalObject->userId;
                $object->password = $originalObject->password;
				if( empty( $object->statusId ) ) $object->statusId = $originalObject->statusId;
            }

            if ( !empty($this->password) ) {
                $object->password = AuthUtility::EncodePassword($this->password, Request::getString( 'su_EncodeMethod' ));
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
            
            return $errors;
        }
        
        
        /**
         * Add Object
         *
         * @param User $object
         * @return bool
         */
        protected function add( $object ) {
            $result = parent::$factory->Add( $object );
            
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
        
        
        /**
         * Set Foreign Lists
         */
        protected function setForeignLists() {}
    }
?>