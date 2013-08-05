<?php
    /**
     * Site
     * @package Eaze
     * @subpackage Site
     * @author sergeyfast
     */
    class Site {

        /**
         * Current Host
         *
         * @var Host
         */
        public static $Host;

        /**
         * Hosts in site
         *
         * @var Host[]
         */
        public static $Hosts = array();

        /**
         * Default Host
         *
         * @var Host
         */
        public static $DefaultHost;

        /**
         * Paths array of name:// => path
         *
         * @var array
         */
        public static $Paths = array();

        /**
         * Current Site name
         *
         * @var string
         */
        public static $Name;

        /**
         * Modules
         *
         * @var array
         */
        public static $Modules = array();

        /**
         * Page
         *
         * @var Page
         */
        public static $Page;


        /**
         * Is Devel
         *
         * @var bool
         */
        private static $isDevel = false;


        /**
         * Get Is Devel <host devel="true">
         *
         * @return boolean
         */
        public static function IsDevel() {
            return self::$isDevel;
        }


        /**
         * Set Paths
         *
         * @param DOMNodeList $paths
         */
        private static function setPaths( DOMNodeList $paths ) {
            /** @var DOMElement $path */
            foreach ( $paths as $path ) {
                self::$Paths[$path->getAttribute( 'name' ) . '://'] = $path->getAttribute( 'value' );
            }
        }


        /**
         * Set Modules
         *
         * @param DOMNodeList $modules
         */
        private static function setModules( DOMNodeList $modules ) {
            if ( ! empty( $modules ) ) {
                /** @var DOMElement $module */
                foreach ( $modules as $module ) {
                    $class = $module->getAttribute( 'class' );
                    if ( !empty( $class ) ) {
                        call_user_func( array( $class, 'Init' ), $module->childNodes );
                    }
                }
            }
        }


        /**
         * Set Databases
         *
         * @param DOMNodeList $databases
         */
        private static function setDatabases( DOMNodeList $databases ) {
            /** @var DOMElement $database */
            foreach ( $databases as $database ) {
                if ( $database instanceof DOMComment ) {
                    continue;
                }

                $dbName = $database->getAttribute( 'name' );
                $param  = array(
                    'driver' => $database->getAttribute( 'driver' )
                    , 'name' => empty( $dbName ) ? 'default' : $dbName
                );

                /// Form array
                foreach ( $database->childNodes as $node ) {
                    if ( $node instanceof DOMComment ) {
                        continue;
                    }

                    $nodeName  = $node->nodeName;
                    $nodeValue = $node->nodeValue;

                    switch ( $nodeName ) {
                        case 'user':
                        case 'password':
                        case 'port':
                        case 'encoding':
                        case 'persistent':
                            $param[$nodeName] = $nodeValue;
                            break;
                        case 'hostname':
                            $param['host']    = $nodeValue;
                            break;
                        case 'name':
                            $param['dbname']  = $nodeValue;
                            break;
                        default:
                            Logger::Warning( 'Unknown key %s', $nodeName );
                            break;
                    }
                }

                ConnectionFactory::Add( $param );
            }
        }


        /**
         * Add Host from DOMNode
         *
         * @param DOMNode|DOMElement $node
         */
        private static function addHost( DOMNode $node ) {
            $host    = $node->getAttribute( 'name' );
            $isDevel = $node->getAttribute( 'devel' );

            $protocol = 'http';
            $default  = false;
            $hostname = '';
            $port     = '';
            $webroot  = '';
            $paths    = array();

            foreach ( $node->childNodes as $child ) {
                if ( $child instanceof DOMComment ) {
                    continue;
                }

                $nodeName  = $child->nodeName;
                $nodeValue = $child->nodeValue;

                switch ( $nodeName ) {
                    case 'webroot':
                    case 'hostname':
                    case 'port':
                    case 'protocol':
                    case 'default':
                        $$nodeName = $nodeValue;
                        break;
                    case 'settings':
                        $pathLookup = XmlHelper::GetLookup( $child );
                        $paths = $pathLookup->Get( 'paths/*' );
                        break;
                    default:
                        Logger::Warning( 'Unknown key %s', $nodeName );
                        break;
                }
            }

            $hostObject = new Host( $protocol, $hostname, $port, $webroot, $default, $host );
            if ( $paths ) {
                $hostObject->SetPaths( $paths );
            }

            if ( $default === 'true' ) {
                self::$DefaultHost = $hostObject;
                if ( $isDevel === 'true' ) {
                    self::$isDevel = true;
                }
            }

            self::$Hosts[$host] = $hostObject;
        }


        /**
         * Init site
         *
         * @param DOMElement $host  the current host
         */
        public static function Init( DOMElement $host ) {
            $siteLookup = XmlHelper::GetLookup( $host->parentNode->parentNode );
            $hostLookup = XmlHelper::GetLookup( $host );

            self::$Name = $host->parentNode->parentNode->getAttribute( 'name' );

            // Set Current Host and it's Localname and default value
            self::$Host = Host::GetCurrentHost();
            self::$Host->SetLocalname( $host->getAttribute( SiteManagerConstants::xmlName ) );
            self::$Host->SetDefault( Convert::ToBoolean(  $host->getElementsByTagName( SiteManagerConstants::xmlDefault )->item( 0 )->nodeValue ) );

            // set is devel
            self::$isDevel = Convert::ToBoolean( $host->getAttribute( SiteManagerConstants::xmlDevel ) );
            if ( is_null( self::$isDevel ) ) {
                self::$isDevel = false;
            }

            foreach ( $host->parentNode->childNodes as $node ) {
                if ( $node instanceof DOMComment ) {
                    continue;
                }

                self::addHost( $node );
            }

            $localSettings = $hostLookup->Get( 'settings' )->item( 0 );
            if ( $localSettings ) {
                self::setPaths( $hostLookup->Get( 'settings/paths/*' ) );
                self::setDatabases( $hostLookup->Get( 'settings/databases/*' ) );
                self::setModules( $hostLookup->Get( 'settings/modules/*' ) );
            } else {
                self::setPaths( $siteLookup->Get( 'settings/paths/*' ) );
                self::setDatabases( $siteLookup->Get( 'settings/databases/*' ) );
                self::setModules( $siteLookup->Get( 'settings/modules/*' ) );
            }
        }


        /**
         * @static
         * @param  $path
         * @param null $hostname
         * @return mixed|string
         */
        public static function TranslateUrlWithPath( $path, $hostname = null ) {
            if ( !empty( $hostname )
                 && !empty( self::$Hosts[$hostname] )
            ) {
                $currentHost = self::$Hosts[$hostname];
                if ( !empty( $currentHost->Paths ) ) {
                    $hostPaths = $currentHost->Paths;
                }
            }

            // Detect path template
            if ( preg_match( '#^.+?://*#i', $path, $regs ) ) {
                $result = $regs[0];

                // Use Normal
                if ( empty( $hostPaths ) ) {
                    if ( !empty( self::$Paths[$result] ) ) {
                        $pathTemplate = self::$Paths[$result];
                    }
                } else { // User HostPaths
                    if ( !empty( $hostPaths[$result] ) ) {
                        $pathTemplate = $hostPaths[$result];
                    }
                }

                if ( !empty( $pathTemplate ) ) {
                    $result = str_replace( $result, $pathTemplate . "/", $path );
                } else {
                    $result = $path;
                }
            } else {
                $result = $path;
            }

            return $result;
        }


        /**
         * Translate Path Template
         *
         * @param string  $path
         * @return string
         */
        public static function TranslatePathTemplate( $path ) {
            // Detect path template
            if ( preg_match( '#^.+://*#i', $path, $regs ) ) {
                $result = $regs[0];

                // Use Normal
                if ( !empty( self::$Paths[$result] ) ) {
                    $pathTemplate = self::$Paths[$result];
                }

                if ( !empty( $pathTemplate ) ) {
                    $result = str_replace( $result, $pathTemplate . '/', $path );
                } else {
                    $result = $path;
                }
            } else {
                $result = $path;
            }

            return $result;
        }


        /**
         * Get Web Path
         *
         * @param string $path
         * @param string $hostname
         * @return string
         */
        public static function GetWebPath( $path, $hostname = null ) {
            $session = '';
            $currentHost = self::$Host;

            /** Set Default Host Path resolution */
            if ( empty( $hostname ) && !empty( self::$DefaultHost ) ) {
                $hostname = self::$DefaultHost->GetLocalname( );
            }

            if ( !empty( $hostname )
                 && !empty( self::$Hosts[$hostname] )
            ) {
                $currentHost = self::$Hosts[$hostname];
                if ( self::$Host->GetProtocol( ) != $currentHost->getProtocol( ) ) {
                    $session = sprintf( '%sPHPSESSID=%s', ( strpos( $path, "?" ) === false ) ? '?' : '&', Session::getId( ) );

                    if ( ( strlen( $path ) > 3 )
                         && ( in_array( substr( $path, strlen( $path ) - 3, 3 ), array( 'gif', 'jpg', 'css' ) ) )
                    ) {
                        $session = "";
                    }
                }
            }

            $result = self::translateUrlWithPath( $path, $hostname );
            $result = sprintf( "%s%s%s", $currentHost->GetPathString(), $result, $session );

            return $result;
        }


        /**
         * Get Web Path with {var} replacement
         * @static
         * @param  string $path
         * @param string $hostname
         * @return mixed
         */
        public static function GetWebPathEx( $path, $hostname = null ) {
            $path = self::GetWebPath( $path, $hostname );

            $parameters = Response::getParameters();
            $keys       = array();
            $values     = array();

            foreach ( $parameters as $key => $value ) {
                if ( is_string( $value ) || is_numeric( $value ) ) {
                    $keys[]   = sprintf( "{%s}", $key );
                    $values[] = $value;
                }
            }

            return str_replace( $keys, $values, $path );
        }


        /**
         * Get Real Path
         *
         * @param string $path
         * @return string
         */
        public static function GetRealPath( $path ) {
            $result = self::translateUrlWithPath( $path );

            $result = sprintf( '%s%s', __ROOT__, $result );
            return $result;
        }


        /**
         * Get Current URL
         *
         * @return string
         */
        public static function GetCurrentURI() {
            static $url;

            if ( empty( $url ) ) {
                if ( strlen( Host::GetCurrentWebroot() ) == 0 ) {
                    $url = Request::getRequestUri();
                } else {
                    $pos = strpos( Request::getRequestUri(), Host::GetCurrentWebroot() );
                    if ( $pos !== false ) {
                        $start = strlen( Host::GetCurrentWebroot() ) + $pos;
                        $end   = strlen( Request::getRequestUri() ) - $start;
                        $url   = substr( Request::getRequestUri(), $start, $end );
                    }
                }
            }

            return $url;
        }
    }

?>