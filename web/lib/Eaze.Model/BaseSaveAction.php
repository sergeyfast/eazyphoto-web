<?php
    /**
     * Base Abstract Save Action
     * @author sergeyfast, shuler
     * @package Eaze
     * @subpackage Model
     */
    abstract class BaseSaveAction {

        /**
         * Add "action" value
         */
        const AddAction = 'add';

        /*
         * Update "action" value
         */
        const UpdateAction = 'update';

        /**
         * Delete "action" value
         */
        const DeleteAction = 'delete';

        /**
         * Allowed Methods for "action"
         * @var string[]
         */
        protected $allowedMethods = array( self::AddAction, self::UpdateAction, self::DeleteAction );

        /**
         * Allowed Redirects
         * @var string[]
         */
        protected $allowedRedirects = array( 'view', 'reopen', 'success' );

        /**
         * Initial Object Id (update action)
         * @var integer
         */
        protected $objectId;

        /**
         * Current Factory
         *
         * @var IFactory
         */
        protected static $factory;

        /**
         * Action: "add", "update" or "delete" (self::*Action constant)
         *
         * @var string
         */
        protected $action;

        /**
         * Redirect: "view", "reopen" or "success" (default)
         * "view" redirects to page with saved object
         * "reopen" redirects to new add form (only for "add" action)
         * "success" redirects to objects list
         *
         * @var string
         */
        protected $redirect;

        /**
         * Current Object
         *
         * @var object
         */
        protected $currentObject;

        /**
         * Original Object
         *
         * @var object
         */
        protected $originalObject;

        /**
         * Search for Get Object
         *
         * @var array
         */
        protected $search = array();

        /**
         * Options for Get Object
         *
         * @var array
         */
        protected $options = array();

        /**
         * Current Errors
         *
         * @var array
         */
        protected $errors = array();

        /**
         * Before Action
         * already has (vars: action, redirect, objectId, originalObject)
         * after method (getSearch)
         *
         * @return void
         */
        protected function beforeAction() {}

        /**
         * Get Search
         *
         * @return array
         */
        protected function getSearch() {
            return Request::getArray( 'search' );
        }

        /**
         * Abstract Get Object From Request
         *
         * @abstract
         * @param object $originalObject object reference from GetById
         * @return object
         */
        abstract protected function getFromRequest( $originalObject = null );

        /**
         * Before Save
         * already has all vars
         * before method (save)
         *
         * @return void
         */
        protected function beforeSave() {}

        /**
         * Abstract Validate
         *
         * @abstract
         * @param object $object  $this->currentObject reference
         * @return array
         */
        abstract protected function validate( $object );

        /**
         * Abstract Add Object
         *
         * @abstract
         * @param object $object $this->currentObject reference
         * @return mixed
         */
        abstract protected function add( $object );

        /**
         * Abstract Update Object
         *
         * @abstract
         * @param object $object  $this->currentObject reference
         * @return mixed
         */
        abstract protected function update( $object );

        /**
         * Delete Object
         *
         * @param object $object  $this->currentObject reference
         * @return mixed
         */
        protected function delete( $object ) {
            return self::$factory->Delete( $object );
        }

        /**
         * Set Foreign Lists
         * @return void
         */
        protected function setForeignLists() {}

        /**
         * After Action
         * - method called after action work before redirect
         * - redirect can be changed in this method
         *
         * @param boolean|null $success
         * @return void
         */
        protected function afterAction( $success ) {}

        /**
         * Entry Point
         *
         * @return mixed
         */
        public function Execute() {
            SecureTokenHelper::Set();

            $this->action   = Request::getString( 'action' );
            $this->redirect = Request::getString( 'redirect' );
            $this->objectId = $objectId = Convert::ToInt( empty( Page::$RequestData[1] ) ? null : Page::$RequestData[1] );

            $this->search = $this->getSearch();
            $object = self::$factory->GetById( $objectId, $this->search, $this->options );
            $this->originalObject = !empty( $object ) ? clone $object : null;

            $this->beforeAction();
            $this->setForeignLists();
            $this->setCurrentTab();

            /**
             * set current object if null
             */
            if ( $this->currentObject === null ) {
                if ( is_null( $object ) ) {
                    $object = $this->getFromRequest();
                } elseif ( $this->action == self::UpdateAction ) {
                    $object = $this->getFromRequest( $object );
                }

                $this->currentObject = $object;
            }

            $this->beforeSave();

            /**
             * set object to response
             */
            Response::setParameter( 'object', $this->currentObject );
            Response::setInteger( 'objectId', $this->objectId );

            /**
             * action filter
             */
            if( !in_array( $this->action, $this->allowedMethods ) ) {
                return null;
            }

            return $this->save( $this->action );
        }


        /**
         * Process DB Operations (add, update, delete)
         * @return string redirect
         */
        protected function save() {
            if ( !SecureTokenHelper::Check() ) {
                return null;
            }


            $result = null;

            $this->errors = $this->validate( $this->currentObject );
            if ( empty( $this->errors ) ) {
                $result = call_user_func_array( array( $this, $this->action ), array( $this->currentObject ) );

                /** db operation error */
                if ( $result === false ) {
                    $this->errors['fatal'] = 'database';
                }
            }

            $this->afterAction( $result );

            /**
             * Action result
             */
            if ( empty( $this->errors ) ) {
                // set correct redirect
                if ( empty( $this->redirect ) || !in_array( $this->redirect, $this->allowedRedirects ) ) {
                    $this->redirect = 'success';
                }

                /**
                 * check if redirect applicable for action
                 */
                if ( $this->action == self::DeleteAction ) {
                    $this->redirect = 'success';
                } else if ( ( $this->action == self::UpdateAction ) && ( $this->redirect == 'reopen' ) ) {
                    $this->redirect = 'success';
                }

                return $this->redirect;
            }

            Response::setArray( 'errors', $this->errors );
            $this->setJsonErrors();

            return null;
        }

        /**
         * translate errors and send them to template in json format
         *
         * @return void
         */
        protected function setJsonErrors() {
            $result = array();

            if( !empty( $this->errors['fields'] ) ) {
                foreach( $this->errors['fields'] as $field => $fieldErrors ) {
                    $result[$field]['title'] = $field;
                    foreach( $fieldErrors as $error ) {
                        $result[$field]['errors'][] = LocaleLoader::Translate( 'errors.' . $error );
                    }
                }
            }

            Response::setString( 'jsonErrors', ObjectHelper::ToJSON( $result ) );
        }

        /**
         * set current tab
         *
         * @return void
         */
        protected function setCurrentTab() {
            $selectedTab = Request::getInteger( "selectedTab" );
            Response::setInteger( "selectedTab", (is_null( $selectedTab ) ? 0 : $selectedTab ) );
        }
    }
?>