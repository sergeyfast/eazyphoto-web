<?php
    use Eaze\Core\Cookie;
    use Eaze\Core\DateTimeWrapper;
    use Eaze\Core\Request;
    use Eaze\Core\Session;
    use Eaze\Site\Page;

    /**
     * Check SimpleAuth Action
     *
     */
    class CheckAuthAction {

        /**
         * Execute CheckAuth
         */
        public function Execute() {
            $class = Request::getString( 'ca_Class' );
            $user  = Session::getObject( $class );

            // Cookie Auth
            if ( !$user ) {
                $authKey = Cookie::GetString( $class );
                $user    = AuthUtility::GetByAuthKey( $authKey );
                if ( $user ) {
                    AuthUtility::Login( $user, $class, true );
                    return null;
                }
            }

            // empty in session = redirect to auth page
            if ( !$user ) {
                Session::setString( '__redirectUrl', Page::$RequestData[0] );
                return 'failure';
            }

            // changed in session = redirect to auth page
            $user = AuthUtility::GetByAuthKey( $user->authKey );
            if ( !$user ) {
                return 'failure';
            }

            /** reset session object */
            $this->updateLastActivity( $user );
            AuthUtility::Login( $user, $class );
            return null;
        }


        /**
         * Update Last Activity Date
         * @param User $user
         */
        public function updateLastActivity( User $user ) {
            if ( !$user->lastActivityAt || $user->lastActivityAt->format( 'U' ) < time() - 90 ) {
                $user->lastActivityAt = DateTimeWrapper::Now();
                UserFactory::UpdateByMask( $user, [ 'lastActivityAt' ], [ 'userId' => $user->userId ] );
            }
        }
    }