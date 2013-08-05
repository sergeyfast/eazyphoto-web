<?php
    /**
     * Login Action
     * @param string la_Class         Object class for login
     * @param string la_EncodeMethod  Encode method (salt or null)
     */
    class LoginAction {
        
        /**
         * Execute
         */
        public function Execute() {
            // Logout
            $class        = Request::getString( "la_Class" );
            $encodeMethod = Request::getString( "la_EncodeMethod");
            
            AuthUtility::Logout( $class );
            
            $loginForm = Request::getInteger( "loginForm");
            $login     = Request::getString( "login" );
            $password  = Request::getString( "password" );
            $password  = AuthUtility::EncodePassword( $password, $encodeMethod );
            Response::setString( 'login', $login );


            if ( $loginForm == 1 ) {
                $user = AuthUtility::GetByCredentials( $login, $password, $class );
                
                if ( !empty( $user ) ) {
                    AuthUtility::Login( $user, $class );
					
					// Redirect to Url If Set
                    $redirectUrl = Session::getString( "__redirectUrl" );
                    if ( !empty( $redirectUrl ) ) {
                        Session::setString( "__redirectUrl",    null );
                        Response::setParameter("__redirectUrl", $redirectUrl );

                        return "url";
                    }
                    
                    return "success";
                }
                
                Response::setString( "error", "vt.login.error" );

                return null;
            }
        }
    }
?>