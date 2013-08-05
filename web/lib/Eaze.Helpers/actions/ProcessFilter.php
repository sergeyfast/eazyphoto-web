<?php
    /**
     * Process Filter Action
     *
     * @author  Sergey Bykov
     * @version $Revision: 1.1 $
     */
    class ProcessFilter {

        static function removePHPFromUri( $uri ) {
            if( strpos( $uri, ".php" , ( count($uri) > 3 ) ? count( $uri) - 4 : null  ) !== false ) {
                $uriInfo = pathinfo( $uri );

                return $uriInfo["dirname"] . "/";
            }

            return  $uri;
        }

        
        /**
         * Execute Action
         *
         * @return string
         */
        public static function Execute() {
            $parameters   = Request::getArray( "parameters" );
            $excludedUrls = Request::getArray( "excludedUrls" );
            $referer      = Request::getReferer();
            $uri          = Request::getRequestUri();
            $scriptName   = Page::$Uri;

            // Get Catched Parameters
            $catchedParameters = Session::getArray( "__catchedParameters" );

            // Set Referer
            if ( false == empty( $referer ) ) {
                $refererInfo = parse_url( $referer );
                $referer     = $refererInfo["path"];

                if ( false == empty( $refererInfo["query"] ) ) {
                    $referer .= "?" .  $refererInfo["query"];
                }
            }

            // Checking for parameters
            if ( false == empty( $parameters ) ) {
                // Check for Right PAge
                if ( $uri == $referer  ) {
                    // Save parameters
                    foreach ($parameters as $parameter) {
                    	$catchedParameters[$scriptName][$parameter] = Request::getParameter( $parameter );
                    }

                    Session::setArray( "__catchedParameters", $catchedParameters );

                    return null;
                }

                // Restore Parameters
                if (strpos( $referer, self::removePHPFromUri(  $uri ) ) !== false ) {
                    foreach ( $excludedUrls as $excludedUrl ) {
                        if ( strpos( $referer, $excludedUrl ) !== false ) {
                            return null;
                        }
                    }

                    $redirectToResponse = Request::getBoolean( "redirectToResponse" );

                    if ( false == empty( $catchedParameters[$scriptName]) ) {
                        foreach ($catchedParameters[$scriptName] as $key => $value ) {
                            Request::setParameter( $key, $value );

                            if ( false == empty( $redirectToResponse ) ) {
                                Response::setParameter( $key, $value );
                            }
                        }
                    }
                } else {
                    // Save parameters
                    foreach ($parameters as $parameter) {
                    	$catchedParameters[$scriptName][$parameter] = Request::getParameter( $parameter );
                    }
                    
                    Session::setArray( "__catchedParameters", $catchedParameters );
                }
            }
        }
    }
?>