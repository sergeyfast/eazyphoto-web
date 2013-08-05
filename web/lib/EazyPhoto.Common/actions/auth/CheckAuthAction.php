<?php
    /**
     * Check SimpleAuth Action
     *
     */
    class CheckAuthAction {
        
        /**
         * Execute CheckAuth
         */
        public function Execute() {
            $class = Request::getString( "ca_Class" );
            $user  = Session::getObject( $class );
            
            // Cookie Auth
            if ( empty( $user ) ) {
                $credentials = Cookie::getArray( $class );
                
                if  ( !empty( $credentials["login"] ) && !empty( $credentials["password"] ) ) {
                    $user = AuthUtility::GetByCredentials( $credentials["login"], $credentials["password"], $class );
                    
                    if ( !empty( $user ) ) {
                        AuthUtility::Login( $user, $class );
                    }
                }
            }
            
            // Request Auth
            if ( empty( $user) ) {
				Session::setString( "__redirectUrl", Page::$RequestData[0] );
				
                return 'failure';
            }
            
            $user = AuthUtility::GetByCredentials( $user->login, $user->password, $class );
            if ( empty( $user ) ) {
                return 'failure';
            }
            
            AuthUtility::ToResponse( $user, $class );

            return null;
        }
    }
?>