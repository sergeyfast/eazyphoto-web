<?php
    /**
     * Action
     *
     * @package Eaze
     * @subpackage Site
     * @author sergeyfast
     */
    class Action {

        /**
         * Package Name
         *
         * @var string
         */
        public $Package;

        /**
         * Action Name
         *
         * @var string
         */
        public $Name;

        /**
         * Full Name (Package.Name)
         *
         * @var string
         */
        public $FullName;

        /**
         * Action FilePath
         *
         * @var string
         */
        public $Path;

        /**
         * Redirects
         *
         * @var array
         */
        public $Redirects;

        /**
         * Request, Response parameters
         *
         * @var array
         */
        public $Parameters = array();


        /**
         * Ready State
         *
         * @var bool
         */
        private $ready = false;

        /**
         * Cached DOMDocuments
         *
         * @var DOMDocument[]
         */
        private static $docs = array();

        /**
         * Cached DOMXPaths
         *
         * @var DOMXPath[]
         */
        private static $xpath = array();


        /**
         * Constructor
         *
         * @param string $package  package name
         * @param string $name     action name
         */
        public function __construct( $package, $name ) {
            $this->Package  = $package;
            $this->Name     = $name;
            $this->FullName = sprintf( '%s.%s', $this->Package, $this->Name );

            if ( empty( self::$docs[$this->Package] ) ) {
                $filePath = sprintf( '%s/%s/%s.xml', __LIB__, $this->Package, $this->Package );

                if ( is_file( $filePath ) ) {
                    $doc = new DOMDocument();
                    $doc->preserveWhiteSpace = false;

                    if ( !$doc->load( $filePath ) ) {
                        Logger::Error( 'Error while loading %s', $filePath );
                        return;
                    }

                    self::$docs[$this->Package]  = $doc;
                    self::$xpath[$this->Package] = new DOMXPath( $doc );
                    Logger::Debug( 'Loaded %s', $this->FullName );
                } else {
                    Logger::Warning( "Couldn't open package file %s", $filePath );
                    return;
                }
            }

            $this->initializeAction();
        }


        /**
         * Process Action
         *
         * @return string  the redirect name
         */
        public function Process() {
            if ( $this->ready ) {
                $actionName     = basename( $this->Path, '.php' );
                $actionInstance = new $actionName();

                foreach ( $this->Parameters as $key => $paramGroup ) {
                    foreach ( $paramGroup as $paramKey => $paramValue ) {
                        switch ( $key ) {
                            case 'request':
                                Request::setParameter( $paramKey, $paramValue );
                                break;
                            case 'response':
                                Response::setParameter( $paramKey, $paramValue );
                                break;
                            case 'session':
                                Session::setParameter( $paramKey, $paramValue );
                                break;
                        }
                    }
                }

                return $actionInstance->execute();
            }

            Logger::Warning( 'Action %s is not ready', $this->FullName );
            return null;
        }


        /**
         * Initialize Action and require_once <action-name>.php
         */
        private function initializeAction() {
            if ( $this->ready ) {
                return;
            }

            if ( empty( self::$xpath[$this->Package] ) ) {
                Logger::Error( "Xpath doesn't exists %s", $this->Package );
                return;
            }

            $action = self::$xpath[$this->Package]->evaluate( sprintf( PageManagerConstants::defaultActionQuery, $this->Name ) )->item( 0 );
            if ( empty( $action ) ) {
                Logger::Error( "Action %s wasn't found", $this->FullName );
                return;
            }

            // GET Action Data
            foreach ( $action->childNodes as $node ) {
                if ( $node instanceof DOMComment ) {
                    continue;
                }

                $nodeName = $node->nodeName;
                switch ( $nodeName ) {
                    case 'path':
                        $this->Path = sprintf( '%s/%s/actions/%s.php', __LIB__, $this->Package, $node->nodeValue );
                        if ( is_file( $this->Path ) ) {
                            /** @noinspection PhpIncludeInspection */
                            require_once( $this->Path );
                        } else {
                            Logger::Error( "File %s doesn't exist", $this->Path );
                            return;
                        }

                        break;
                    case 'parameters':
                        foreach ( $node->childNodes as $pNode ) {
                            if ( $pNode instanceof DOMComment ) {
                                continue;
                            }

                            switch ( $pNode->nodeName ) {
                                case 'session':
                                case 'request':
                                case 'response':
                                    foreach ( $pNode->childNodes as $paramNode ) {
                                        if ( $paramNode instanceof DOMComment ) {
                                            continue;
                                        }

                                        $paramName = $paramNode->getAttribute( 'name' );
                                        $this->Parameters[$pNode->nodeName][$paramName]=   eval( 'return ' . $paramNode->nodeValue . ';' );
                                    }
                                    break;
                                default:
                                    Logger::Warning( 'Unknown  %s in %s xml|parameters', $pNode->nodeName, $this->FullName );
                                    break;
                            }
                        }
                        break;
                    case 'redirects':
                        foreach ( $node->childNodes as $rNode ) {
                            if ( $rNode instanceof DOMComment ) {
                                continue;
                            }

                            $this->Redirects[$rNode->getAttribute( 'name' )] = array(
                                'path'   => $rNode->getAttribute( 'path' )
                                , 'host' => $rNode->getAttribute( 'host' )
                            );
                        }
                        break;
                    default:
                        Logger::Warning( 'Unknown %s in %s .xml', $nodeName, $this->FullName );
                        break;
                }
            }

            // Load Default Path if needed
            if ( empty( $this->Path ) ) {
                $this->Path = sprintf( '%s/%s/actions/%s.php', __LIB__, $this->Package, $this->Name );
                if ( is_file( $this->Path ) ) {
                    Logger::Debug( 'Loading action %s', $this->FullName );
                    /** @noinspection PhpIncludeInspection */
                    require_once( $this->Path );
                } else {
                    Logger::Error( "File %s doesn't exist", $this->Path );
                    return;
                }
            }

            $this->ready = true;
        }
    }

?>