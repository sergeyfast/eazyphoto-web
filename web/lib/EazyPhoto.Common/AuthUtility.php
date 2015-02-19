<?php
    use Eaze\Core\Cookie;
    use Eaze\Core\Response;
    use Eaze\Core\Session;

    /**
     * Simple Auth Utility
     */
    class AuthUtility {

        /**
         * Salt
         * @var string
         */
        public static $Salt = 'saltedp@$$-';

        /**
         * Login Cookie Lifetime in seconds (1 week)
         */
        const LoginCookieLifeTime = 604800;


        /**
         * Get User By Auth Key
         *
         * @param $authKey
         * @return User
         */
        public static function GetByAuthKey( $authKey ) {
            if ( !$authKey ) {
                return null;
            }

            return UserFactory::GetOne( [ 'authKey' => $authKey ] );
        }


        /**
         * @param string $login
         * @param string $password encoded password
         * @return User
         */
        public static function GetByLogin( $login, $password ) {
            if ( !$login || !$password ) {
                return null;
            }

            return UserFactory::GetOne( [ 'login' => $login, 'password' => $password ] );
        }


        /**
         * Encode / Salt Password
         *
         * @param string $password password
         * @param string $type     salt or md5
         * @return string
         */
        public static function EncodePassword( $password, $type = 'salt' ) {
            switch ( $type ) {
                case 'salt':
                    return md5( self::$Salt . md5( self::$Salt . $password ) );
                case 'md5':
                    return md5( $password );
            }

            return $password;
        }


        /**
         * Get Current User from Session
         *
         * @param string $class
         * @return object
         */
        public static function GetCurrentUser( $class ) {
            $user = Session::GetParameter( $class );

            return $user;
        }


        /**
         * Login User
         *
         * @param User   $user
         * @param string $class
         * @param bool   $setCookie
         */
        public static function Login( User $user, $class, $setCookie = false ) {
            if ( $setCookie ) {
                Cookie::SetCookie( $class, $user->authKey, time() + self::LoginCookieLifeTime );
            }

            Session::SetParameter( $class, $user );
            Session::SetParameter( $class . 'Logged', true );

            AuthUtility::ToResponse( $user, $class );
        }


        /**
         * Logout User
         *
         * @param string $class
         */
        public static function Logout( $class ) {
            Cookie::SetCookie( $class, '', time() - 3600 );

            Session::SetParameter( $class, null );
            Session::SetParameter( $class . 'Logged', false );
        }


        /**
         * Set Variables To Response
         *
         * @param object $user
         * @param string $class
         */
        public static function ToResponse( $user, $class ) {
            Response::SetParameter( '__' . $class, $user );
            Response::SetBoolean( '__' . $class . 'Logged', true );
        }


        /**
         * Generate New AuthKey
         * @param User $user
         * @param bool $isRandom
         * @return string
         */
        public static function NewAuthKey( User $user, $isRandom = false ) {
            if ( $isRandom ) {
                return self::GeneratePassword( 32, true, true, false );
            }

            return self::EncodePassword( md5( implode( ':', [ $user->login, $user->password, $user->userId ] ) ) );
        }


        /**
         * Update User Auth Key
         * @param User $user
         * @param bool $isRandom
         * @return array|bool
         */
        public static function UpdateAuthKey( User $user, $isRandom = false ) {
            $user->authKey        = self::NewAuthKey( $user, $isRandom );
            $user->lastActivityAt = \Eaze\Core\DateTimeWrapper::Now();

            return UserFactory::Update( $user );
        }


        /**
         * Generate random strong password
         * @static
         * @param int  $pw_length
         * @param bool $use_caps
         * @param bool $use_numeric
         * @param bool $use_specials
         * @return string
         */
        public static function GeneratePassword( $pw_length = 8, $use_caps = true, $use_numeric = true, $use_specials = true ) {
            $caps       = [ ];
            $numbers    = [ ];
            $reg_length = $pw_length;
            $pws        = [ ];
            $chars      = [ ];
            $signs      = [ ];
            $pw         = [ ];
            for ( $ch = 97; $ch <= 122; $ch++ ) {
                $chars[] = $ch;
            } // create a-z
            if ( $use_caps ) {
                for ( $ca = 65; $ca <= 90; $ca++ ) {
                    $caps[] = $ca;
                }
            } // create A-Z
            if ( $use_numeric ) {
                for ( $nu = 48; $nu <= 57; $nu++ ) {
                    $numbers[] = $nu;
                }
            } // create 0-9
            $all = array_merge( $chars, $caps, $numbers );
            if ( $use_specials ) {
                $reg_length   = ceil( $pw_length * 0.75 );
                $num_specials = $pw_length - $reg_length;
                if ( $num_specials > 5 ) {
                    $num_specials = 5;
                }
                for ( $si = 33; $si <= 47; $si++ ) {
                    $signs[] = $si;
                }
                $rs_keys = array_rand( $signs, $num_specials );
                foreach ( $rs_keys as $rs ) {
                    $pws[] = chr( $signs[$rs] );
                }
            }
            $rand_keys = array_rand( $all, $reg_length );
            foreach ( $rand_keys as $rand ) {
                $pw[] = chr( $all[$rand] );
            }
            $compl = array_merge( $pw, $pws );
            shuffle( $compl );
            return implode( '', $compl );
        }
    }