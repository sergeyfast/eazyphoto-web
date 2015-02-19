<?php
    use Eaze\Core\Request;
    use Eaze\Core\Response;
    use Eaze\Core\Session;
    use Eaze\Helpers\SecureTokenHelper;

    /**
     * Login Action
     * @param string "la_Class"         Object class for login
     * @param string "la_EncodeMethod"  Encode method (salt or null)
     */
    class LoginAction {

        /**
         * Execute
         */
        public function Execute() {
            // Logout
            $class        = Request::getString( 'la_Class' );
            $encodeMethod = Request::getString( 'la_EncodeMethod' );
            $remember     = Request::GetBoolean( 'remember' );
            AuthUtility::Logout( $class );

            $login    = Request::getString( 'login' );
            $password = Request::getString( 'password' );
            $password = AuthUtility::EncodePassword( $password, $encodeMethod );

            if ( SecureTokenHelper::Check() ) {
                $user = AuthUtility::GetByLogin( $login, $password, $class );

                if ( $user && AuthUtility::UpdateAuthKey( $user, !$remember ) ) {
                    AuthUtility::Login( $user, $class, $remember );
                    return $this->getReturnRedirect();
                }

                Response::setString( 'error', 'vt.login.error' );
            }

            Response::SetString( 'login', $login );
            Response::SetBoolean( 'remember', $remember );

            return null;
        }


        /**
         * Get Return Redirect
         * @return string
         */
        private function getReturnRedirect() {
            $redirectUrl = Session::getString( '__redirectUrl' );
            if ( $redirectUrl ) {
                Session::setString( '__redirectUrl', null );
                Response::setParameter( '__redirectUrl', $redirectUrl );

                return 'url';
            }

            return 'success';
        }
    }