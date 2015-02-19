<?
    use Eaze\Model\BaseFactory;
    use Eaze\Model\ObjectInfo;
    use Eaze\Site\Page;

    /**
     * Context: Current Variables
     * @package    EazyPhoto
     * @subpackage Site
     * @author     Sergeyfast
     */
    class Context {

        /** Site Section */

        /**
         * Current Active Section From Menu
         * @var string
         */
        public static $ActiveSection;

        /**
         * Navigation
         * @var Navigation
         */
        public static $Navigation;

        /**
         * Current Main Object
         * @var mixed
         */
        public static $Object;

        /**
         * Get Meta Detail
         * @var MetaDetail
         */
        public static $MetaDetail;

        /**
         * Main Menu Navigations with null parent id
         * @var Navigation[]
         */
        public static $HeaderNav = [ ];

        /**
         * Footer Navigations
         * @var Navigation[]
         */
        public static $FooterNav = [ ];

        /**
         * Current Context Navigations
         * @var Navigation[]
         */
        public static $Navigations = [ ];

        /**
         * Breadcrumbs Queue
         * @var array of Navigation[]
         */
        private static $breadcrumbs = [ 'before' => [ ], 'after' => [ ] ];

        /**
         * Object Info
         * @var ObjectInfo
         */
        private static $objectInfo;


        /**
         * Set Object
         * @param string $object
         */
        public static function SetObject( $object ) {
            self::$Object     = $object;
            self::$objectInfo = ObjectInfo::Get( $object );
        }


        /**
         * Set Context Navigations
         * @param Navigation[] $navigations one type
         */
        public static function SetNavigations( $navigations ) {
            self::$Navigations = $navigations;
        }


        /**
         * Get Url w/o get params
         * @var string
         * @return string
         */
        public static function GetUrl() {
            return Page::$RequestData[0];
        }


        /**
         * Load Main Navigations
         * @return bool
         */
        public static function LoadMainNavigations() {
            $navigations = NavigationFactory::Get( [ ], [ BaseFactory::OrderBy => ' "navigationTypeId", "orderNumber" ' ] );

            foreach ( $navigations as $n ) {
                switch ( $n->navigationType->alias ) {
                    case NavigationUtility::MainMenu:
                        self::$HeaderNav[$n->navigationId] = $n;
                        break;
                    case NavigationUtility::FooterMenu:
                        self::$FooterNav[$n->navigationId] = $n;
                        break;
                }
            }

            Context::SetNavigations( self::$HeaderNav );

            return !empty( $navigations );
        }


        /**
         * Find Navigation By Url
         * @param string $url
         * @return Navigation|null
         */
        public static function FindNavigationByUrl( $url ) {
            $result = null;
            if ( !$url || !self::$Navigations ) {
                return $result;
            }

            foreach ( self::$Navigations as $n ) {
                if ( $n->GetLink() === $url ) {
                    $result = $n;
                    break;
                }
            }

            return $result;
        }


        /**
         * Detect and Set Meta Detail
         * Priority: object, behavior, url
         * @return MetaDetail
         */
        public static function DetectMeta() {
            self::$MetaDetail = MetaDetailUtility::GetForContext( self::$objectInfo, self::GetUrl() );

            return self::$MetaDetail;
        }


        /**
         * @param string $title
         * @param string $url
         * @param bool   $isAfter
         */
        public static function AddBreadcrumb( $title, $url = null, $isAfter = true ) {
            $n        = new Navigation();
            $n->url   = $url;
            $n->title = $title;

            self::$breadcrumbs[$isAfter ? 'after' : 'before'][] = $n;
        }


        /**
         * Add Translated Breadcrumb from fe.breadcrumbs.<alias>
         * @param string $alias
         * @param string $url
         * @param bool   $isAfter
         */
        public static function AddBreadcrumbT( $alias, $url = null, $isAfter = true ) {
            self::AddBreadcrumb( T( 'fe.breadcrumbs.' . $alias ), $url, $isAfter );
        }


        /**
         * Get Breadcrumbs array from Current Navigation
         * @return Navigation[]
         */
        public static function GetBreadcrumbs() {
            $n        = new Navigation();
            $n->url   = '/';
            $n->title = 'Главная';
            $result   = [ $n ];

            // add from local queue
            foreach ( self::$breadcrumbs['before'] as $navigation ) {
                $result[] = $navigation;
            }

            // add from local queue
            foreach ( self::$breadcrumbs['after'] as $navigation ) {
                $result[] = $navigation;
            }


            return $result;
        }


        /**
         * Set Current Navigation
         * @param Navigation $navigation
         */
        public static function SetNavigation( $navigation ) {
            self::$Navigation = $navigation;
        }


        /**
         * Detect and Set Navigation
         * Priority: object, active section, url
         */
        public static function DetectNavigation() {
            if ( !self::$Navigation ) {
                if ( self::$objectInfo ) {
                    if ( self::$objectInfo->Class === StaticPageUtility::ObjectClass ) {
                        foreach ( self::$Navigations as $n ) {
                            if ( self::$objectInfo->Id === $n->staticPageId ) {
                                self::$Navigation = $n;
                                break;
                            }
                        }

                        // try to find via parent static page
                        if ( !self::$Navigation && self::$Object->parentStaticPageId ) {
                            foreach ( self::$Navigations as $n ) {
                                if ( self::$Object->parentStaticPageId === $n->staticPageId ) {
                                    self::$Navigation = $n;
                                    self::AddBreadcrumb( self::$Object->title, self::$Object->url );
                                    break;
                                }
                            }

                            if ( !self::$Navigation ) {
                                self::AddBreadcrumb( self::$Object->title, self::$Object->url );
                            }
                        }
                    }
                } else if ( self::$ActiveSection ) {
                    self::$Navigation = self::FindNavigationByUrl( self::$ActiveSection ); // use url as sections
                } else {
                    self::$Navigation = self::FindNavigationByUrl( self::GetUrl() );
                }
            }

            return self::$Navigation;
        }
    }