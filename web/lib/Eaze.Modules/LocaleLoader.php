<?php
    /**
     * Locale Loader
     *
     * @desc Module Parameters
     * path:        path to language files, default is lang://
     * default:     default language, required
     * allowChange: allow change, default is false
     * LC_*:        set locale
     * htmlEncoding: set encoding for html pages (default is utf-8);
     * @package Eaze
     * @subpackage Modules
     * @static
     * @author sergeyfast
     */
    class LocaleLoader implements IModule {

        /**
         * Russian
         */
        const Ru = 'ru';

        /**
         * Russian
         */
        const En = 'en';

        /**
         * Module Parameters
         *
         * @var array
         */
        private static $params = array();


        /**
         * setlocale Module Parameters
         * @var array
         */
        private static $locales = array();

        /**
         * Initialized Flag
         *
         * @var boolean
         */
        public static $Initialized = false;

        /**
         * Default Path constant
         */
        const defaultPath = 'lang://';

        /**
         * Session Language Key
         *
         */
        const defaultSessionKey = '__language';

        /**
         * Current Language
         *
         * @var string
         */
        public static $CurrentLanguage = '';


        /**
         * Current HTML Encoding
         *
         * @var string
         */
        public static $HtmlEncoding = 'utf-8';


        /**
         * Language Messages
         *
         * @var array
         */
        private static $messages = array();


        /**
         * Init Module
         *
         * @param DOMNodeList $params
         */
        public static function Init( DOMNodeList $params ) {
            foreach ( $params as $param ) {
                $nodeName = $param->getAttribute( 'name' );
                if ( strpos( $nodeName, 'LC_' ) === 0 ) {
                    self::$locales[$nodeName] = $param->nodeValue;
                } else {
                    self::$params[$nodeName]  = $param->nodeValue;
                }
            }

            if ( empty( self::$params['path'] ) ) {
                self::$params['path'] = self::defaultPath;
            }

            if ( !empty( self::$params['htmlEncoding'] ) ) {
                self::$HtmlEncoding = self::$params['htmlEncoding'];
            } else {
                self::$params['html-encoding'] = self::$HtmlEncoding;
            }

            self::$Initialized = true;
            self::Process();
        }


        /**
         * Process module
         *
         */
        public static function Process() {
            if ( !self::$Initialized ) {
                Logger::Error( "Module isn't in Initialized state" );
                return;
            }

            // setlocale
            foreach( self::$locales as $categoryName => $value ) {
                setlocale( constant( $categoryName ), $value );
            }

            // detect current lang
            self::$CurrentLanguage = self::detectLanguage();
            self::LoadLanguage( self::$CurrentLanguage );
        }


        /**
         * Detect Language
         *
         * @return string
         */
        private static function detectLanguage() {
            if ( empty( self::$params['allowChange'] ) ) {
                self::$params['allowChange'] = 'false';
            }

            if ( 'false' == self::$params['allowChange'] ) {
                return self::$params['default'];
            }

            // change lang from post
            $lang = Request::getString( 'lang' );
            if ( false == empty( $lang ) ) {
                if ( file_exists( Site::GetRealPath( self::$params['path'] . $lang . '.xml' ) ) ) {
                    Session::setString( self::defaultSessionKey, $lang );
                    return $lang;
                }
            }

            //set lang from session
            $lang = Session::getString( self::defaultSessionKey );
            if ( false == empty( $lang ) ) {
                Session::setString( self::defaultSessionKey, $lang );
                return $lang;
            }

            // return default
            Session::setString( self::defaultSessionKey, self::$params['default'] );
            return self::$params['default'];
        }


        /**
         * Load Language from php file
         *
         * @param string $lang
         */
        public static function LoadLanguage( $lang ) {
            $filepath = CacheManager::GetCachedFilePath( Site::GetRealPath( self::$params['path'] . $lang . '.xml' ), '%s_%s.lng', array( 'LocaleLoader', 'Cache' ), $lang );

            $l = array();

            /** @noinspection PhpIncludeInspection */
            include_once( $filepath );
            if( !array_key_exists( $lang, self::$messages ) ) {
                self::$messages[$lang] = $l;
            }
        }


        /**
         * Cache Language File
         *
         * @param CacheManagerData $data
         */
        public static function Cache( CacheManagerData $data ) {
            $t = $data->data;

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadXML( $t );

            $parsedPHP = self::parse( $doc );

            if ( $doc->xmlEncoding !== 'utf-8' ) {
                $parsedPHP = iconv( 'utf-8', $doc->xmlEncoding, $parsedPHP );
            }

            $data->data = $parsedPHP;
        }


        /**
         * Parse XML File To PHP file
         *
         * @param DOMDocument $doc
         * @return string
         */
        private static function parse( DOMDocument $doc ) {
            $data = new CacheManagerData( "<?php \n" );

            foreach ( $doc->childNodes as $node ) {
                self::parseGroup( $node, $data, null );
            }

            $data->data .= '?>';
            return $data->data;
        }


        /**
         * Parse XML Group to PHP array def
         *
         * @param DOMElement $node
         * @param CacheManagerData $data
         * @param string $path
         */
        private static function parseGroup( DOMElement $node, CacheManagerData $data, $path ) {
            foreach ( $node->childNodes as $childNode ) {

                if ( !empty( $childNode->firstChild ) && $childNode->firstChild instanceof DOMText ) {
                    $data->data .= '    $l' . "['" . $path . $childNode->nodeName . "'] = '" . addcslashes( $childNode->nodeValue, "'" ) . "';\n";
                } else {
                    if ( $childNode->hasChildNodes() && count( $childNode->childNodes ) > 0 ) {
                        $k = $path . $childNode->nodeName . ".";
                        self::parseGroup( $childNode, $data, $k );
                    }
                }
            }
        }


        /**
         * Translate Message
         *
         * @param string $message  the message string
         * @param mixed  $args     args for sprintf
         * @return string
         */
        public static function Translate( $message, $args = null ) {
            if ( !self::$Initialized ) {
                return $message;
            }

            if ( !empty( self::$messages[self::$CurrentLanguage][$message] ) ) {
                $message = str_replace( "\'", "'", self::$messages[self::$CurrentLanguage][$message] );
            }

            if ( $args !== null ) {
                $message = vsprintf( $message, array_slice( func_get_args(), 1  ) );
            }

            return $message;
        }


        /**
         * Convert From Win1251 To UTF8 if current language != utf8
         * @param string $value
         * @return string
         */
        public static function TryToUTF8( $value ) {
            if ( mb_detect_encoding( $value, 'CP1251,UTF-8' ) != 'UTF-8' ) {
                $value = TextHelper::ToUTF8( $value );
            }

            return $value;
        }


        /**
         * Convert From UTF8 to Win1251 if current language != utf8
         * @param string $value
         * @return string
         */
        public static function TryFromUTF8( $value ) {
            if ( mb_detect_encoding( $value, 'CP1251,UTF-8' ) != 'UTF-8' ) {
                $value = TextHelper::FromUTF8( $value );
            }

            return $value;
        }
    }


    /**
     * LocaleLoader::Translate alias
     *
     * @param string $message  the message string
     * @param mixed  $args     args for sprintf
     * @return string
     */
    function T( $message, $args = null ) {
        return LocaleLoader::Translate( $message, $args );
    }

?>
