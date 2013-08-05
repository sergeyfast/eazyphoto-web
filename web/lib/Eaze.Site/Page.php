<?php
    /**
     * Page
     * @package Eaze
     * @subpackage Eaze.Site
     */
    class Page {

        /**
         * Actions
         *
         * @var Action[]
         */
        public static $Actions = array();

        /**
         * Uri
         *
         * @var string
         */
        public static $Uri;

        /**
         * Template Path
         *
         * @var string
         */
        public static $TemplatePath;

        /**
         * RequestData from Regs
         *
         * @var array
         */
        public static $RequestData;


        /**
         * Constructor
         *
         * @param DOMElement  $page
         * @param array       $regs
         * @param DOMNodeList $virtualActions
         */
        public function __construct( DOMElement $page, array $regs, DOMNodeList $virtualActions ) {
            self::$RequestData = $regs;
            self::$Uri         = $page->getAttribute( 'uri' );

            Package::BeginUri( Host::GetCurrentKey() . self::$Uri );

            self::setTemplate( $page );
            $actions = self::getActionsArray( $page, $virtualActions );

            // Form Packages
            $packages = array();
            foreach ( $actions as $action ) {
                if ( trim( $action ) == '' ) {
                    continue;
                }

                if ( preg_match( '#(.*\\..*)\\.(.*)#', $action, $regs ) ) {
                    $packages[$regs[1]][] = $regs[2];
                    self::$Actions[$action] = new Action( $regs[1], $regs[2] );

                    //Process Action
                    if ( !empty( self::$Actions[$action] ) ) {
                        $redirect = self::$Actions[$action]->Process();

                        // Check For Redirect
                        if ( !empty( $redirect )
                             && ( !empty( self::$Actions[$action]->Redirects[$redirect] ) ) )
                        {
                            Request::Commit();
                            Response::Redirect(
                                Site::GetWebPathEx(
                                    self::$Actions[$action]->Redirects[$redirect]['path']
                                    , self::$Actions[$action]->Redirects[$redirect]['host'] )
                            );
                        }
                    }
                } else {
                    Logger::Warning( 'Invalid action format: %s', $action );
                }
            }

            Request::Commit();

            // Process Template
            // simple include
            if ( ! empty( self::$TemplatePath ) ) {
                Template::Load( Site::GetRealPath( self::$TemplatePath ) );
            }
        }


        /**
         * Set Template Path
         *
         * @param DOMElement $page
         */
        private static function setTemplate( DOMElement $page ) {
            $template = '';
            $templateNode = $page->getElementsByTagName( 'template' )->item( 0 );
            if ( !empty( $templateNode ) ) {
                self::$TemplatePath = $templateNode->nodeValue;
            }
        }


        /**
         * Get Formatted Actions
         *
         * @param DOMElement $page
         * @param DOMNodeList $virtualActions
         * @return array
         */
        private static function getActionsArray( DOMElement $page, DOMNodeList $virtualActions ) {
            // Add virtual actions
            $vActionSearch = $vActionReplace = array();
            foreach ( $virtualActions as $vAction ) {
                $vActionSearch[]  = $vAction->getAttribute( 'name' );
                $vActionReplace[] = $vAction->nodeValue;
            }

            $boot        = $page->getAttribute( PageManagerConstants::xmlBoot );
            $shutdown    = $page->getAttribute( PageManagerConstants::xmlShutdown );
            $actionsNode = $page->getElementsByTagName( 'actions' )->item( 0 );
            $actions     = '';
            if ( !empty( $actionsNode ) ) {
                $actions = $actionsNode->nodeValue;
            }

            // Collect actions list
            $actionsList    = trim( sprintf( '%s,%s,%s', $boot, $actions, $shutdown ), ' , ' );
            if ( !empty( $vActionSearch ) ) {
                $actionsList = str_replace( $vActionSearch, $vActionReplace, $actionsList );
            }

            $actionsList    = str_replace( array( ' ', ',,' ), array( '', ',' ), $actionsList );
            $actionsArrList = explode( ',', $actionsList );

            return $actionsArrList;
        }
    }

?>