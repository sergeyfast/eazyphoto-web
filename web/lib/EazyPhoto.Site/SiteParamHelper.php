<?php
    /**
     * SiteParamHelper
     *
     * @author Sergeyfast
     *
     * @method GetEmail
     * @method GetYandexAPI
     * @method GetYandexMeta
     * @method GetYandexMetrika
     * @method GetGoogleMeta
     * @method GetGoogleAnalytics
     * @method GetGoogleAPI
     * @method GetBigImageQuality
     * @method GetBigImageSize
     * @method GetSmallImageQuality
     * @method GetSiteHeader
     * @method GetSiteFooter

     * @method HasEmail
     * @method HasYandexAPI
     * @method HasYandexMeta
     * @method HasYandexMetrika
     * @method HasGoogleMeta
     * @method HasGoogleAnalytics
     * @method HasGoogleAPI
     * @method HasBigImageQuality
     * @method HasBigImageSize
     * @method HasSmallImageQuality
     * @method HasSiteHeader
     * @method HasSiteFooter
     */
    class SiteParamHelper {
        const Email   			  = 'Email';
        const YandexAPI 		  = 'Yandex.API';
        const YandexMeta 		  = 'Yandex.Meta';
        const YandexMetrika		  = "Yandex.Metrika";
        const GoogleMeta 		  = 'Google.Meta';
        const GoogleAnalytics 	  = 'Google.Analytics';
        const GoogleAPI           = 'Google.API';
        const BigImageQuality     = 'BigImage.Quality';
        const BigImageSize        = 'BigImage.Size';
        const SmallImageQuality   = 'SmallImage.Quality';
        const SiteHeader          = 'Site.Header';
        const SiteFooter          = 'Site.Footer';

        /**
         * @var SiteParamHelper
         */
        private static $instance;

        /**
         * @var string[]
         */
        private static $constantsMapping = array();

        /**
         * @var SiteParam[]
         */
        public static $SiteParams = array();

        /**
         * Initialized Flag
         * @var bool
         */
        private static $isInitialized = false;

        /**
         * Fill SiteParams to
         * @static
         */
        public static function Init() {
            if ( !self::$isInitialized ) {
                self::$SiteParams    = SiteParamFactory::Get( array(), array( BaseFactory::WithoutPages => true ) );
                self::$SiteParams    = BaseFactoryPrepare::Collapse( self::$SiteParams, 'alias', false );
                self::$isInitialized = true;

                $constants = new ReflectionClass( __CLASS__ );
                self::$constantsMapping = $constants->getConstants();
            }
        }


        /**
         * Get SiteParams
         * @static
         * @return SiteParam[]
         */
        public static function GetSiteParams() {
            self::Init();
            return self::$SiteParams;
        }


        /**
         * Magic Method Wrapper
         * @param string $method
         * @param array $params
         * @return string
         */
        public function __call( $method, $params ) {
            // Initialize SiteParams
            if ( !self::$isInitialized ) {
                self::Init();
            }

            $type   = substr( $method, 0, 3 );
            $method = substr( $method, 3 );
            $alias  = ArrayHelper::GetValue( self::$constantsMapping, $method );

            if ( !empty( $alias ) ) {
                switch( $type ) {
                    case 'Get':
                        if ( !empty( self::$SiteParams[$alias] ) ) {
                            return self::$SiteParams[$alias]->value;
                        }
                        break;
                    case 'Has':
                        return ( !empty( self::$SiteParams[$alias] ) );
                        break;
                    default:
                        Logger::Warning( 'Undefined method %s', $method );
                        break;
                }
            }

            return null;
        }



        /**
         * Get Value
         * @static
         * @param string $alias
         * @return mixed
         */
        public static function GetValue( $alias ) {
            if ( !self::$isInitialized ) {
                self::Init();
            }

            if ( !empty( self::$SiteParams[$alias] ) ) {
                return self::$SiteParams[$alias]->value;
            }

            return null;
        }


        /**
         * Get Instance
         * @static
         * @return SiteParamHelper
         */
        public static function GetInstance() {
            if ( self::$instance === null ) {
                self::$instance = new SiteParamHelper();
            }

            return self::$instance;
        }
    }
?>