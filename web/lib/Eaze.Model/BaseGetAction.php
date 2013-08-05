<?php
    /**
     * Base Abstract Get Action
     *
     * @author sergeyfast
     * @package Eaze
     * @subpackage Model
     */
    abstract class BaseGetAction {

        /**
         * Current Factory
         *
         * @var IFactory
         */
        public static $factory;

        /**
         * @var string
         */
        protected $connectionName;

        /**
         * With IFactory::Count()
         *
         * @var bool
         */
        protected $withCount = true;

        /**
         * @var array
         */
        protected $list = array();

        /**
         * Options for Get Object
         *
         * @var array
         */
        protected $options = array(
            BaseFactory::WithoutDisabled => false
            , BaseFactory::WithLists  => true
        );

        /**
         * Available for sort fields
         *
         * @var array
         */
        protected $sortFields = array();

        /**
         * Page number [0..n]
         *
         * @var int
         */
        protected $page;

        /**
         * Objects per page count
         *
         * @var int
         */
        protected $pageSize;

        /**
         * Pages found count
         *
         * @var int
         */
        protected $pageCount;

        /**
         * Objects found count
         *
         * @var int
         */
        protected $objectCount;
        
        /**
         * Search Array
         *
         * @var array
         */
        protected $search = array();

        /**
         * Get Sort
         *
         * @return void
         */
        protected function getSort() {
            $sortField  = Request::getString( 'sortField' );
            $sortType   = Request::getString( 'sortType' );
            $mapping    = BaseFactory::GetMapping( get_class( self::$factory ) );

            //building sort fields array
            foreach( $mapping['fields'] as $field ) {
                $this->sortFields[] = $field['name'];
            }

            if( !empty( $sortField ) && ( !in_array( $sortField, $this->sortFields ) ) ) {
                $sortField = null;
            }
            if( !empty( $sortField ) && !in_array( $sortType, array( 'ASC', 'DESC' ) ) ) {
                $sortType = 'ASC';
            }
            if( !empty( $sortField ) ) {
                $this->options[BaseFactory::OrderBy] = array(
                    array( 'name' => $sortField, 'sort' => $sortType )
                );
            }

            Response::setString( 'sortField', $sortField );
            Response::setString( 'sortType', $sortType );
        }

        /**
         * Get Search
         *
         * @return array
         */
        protected function getSearch() {
            return Request::getArray( 'search' );
        }

        /**
         * Before Action
         * after method (getSearch)
         *
         * @return void
         */
        protected function beforeAction() {}

        /**
         * Set Foreign Lists
         *
         * @return void
         */
        protected function setForeignLists() {}

        /**
         * After Action
         * - method called after action work
         *
         * @return void
         */
        protected function afterAction() {}

        /**
         * Execute Action
         *
         * @return string
         */
        public function Execute() {
            SecureTokenHelper::Set();
            
            $this->getSort();

          	$this->search = self::$factory->ValidateSearch( $this->getSearch() );

            $this->beforeAction();

            $this->page         = Request::getInteger( "page" );
            $this->pageSize     = !empty( $this->search["pageSize"] ) ? $this->search["pageSize"] : 0;
            $this->pageCount    = 0;

            if ( $this->withCount && self::$factory->CanPages() ) {
                $this->pageCount    = self::$factory->Count( $this->search, $this->options, $this->connectionName );
                $this->page         = ( $this->page >= $this->pageCount || $this->page < 0 ) ? 0 : $this->page;
                $this->objectCount  = ceil( $this->pageCount * $this->pageSize );

                $this->search["page"] = $this->page;
            }

            $this->list = self::$factory->Get( $this->search, $this->options, $this->connectionName );

			$this->setForeignLists();
            $this->afterAction();

            Response::setString( 'hideSearch',  Cookie::getString( 'hideSearch' ));
            Response::setArray( 'search',       $this->search );
            Response::setArray( 'list',         $this->list );
            Response::setInteger( 'page',       $this->page );
            Response::setInteger( 'pageSize',   $this->pageSize );
            Response::setFloat( 'pageCount',    $this->pageCount );
            Response::setFloat( 'objectCount',  $this->objectCount );
        }
    }
?>