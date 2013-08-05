<?php
    /**
     * Simple Auth Utility
     */
    class AuthUtility {

        /**
         * Salt
         * @var string
         */
        public static $Salt = '321p@$$-';

        /**
         * Login Cookie Lifetime in seconds
         */
        const LoginCookieLifeTime = 180000;



        /**
         * Get User By Credentials
         *
         * @param string $login
         * @param string $password
         * @param string $class
         * @param string $connectionName
         * @return User
         */
        public static function GetByCredentials( $login, $password, $class, $connectionName = null ) {
            if ( ( empty( $login ) ) || empty( $password ) ) {
                return null;
            }

            $factory     = BaseFactory::GetInstance( $class . 'Factory' );
            $searchArray = array( "login" => $login, "password" => $password );
            $options     = array( BaseFactory::WithoutDisabled => true, BaseFactory::WithLists => true );
            
            $object      = $factory->GetOne( $searchArray, $options, $connectionName );

            return $object;
        }


        /**
         * Encode / Salt Password
         * 
         * @param string $password  password
         * @param string $type      salt or md5
         * @return string
         */
        public static function EncodePassword( $password, $type = 'salt' ) {
            switch( $type ) {
                case 'salt':
                    return md5( self::$Salt . md5(  self::$Salt . $password  ));
                case 'md5':
                    return md5( $password );
            }

            return $password;
        }


        /**
         * Get Current User
         *
         * @param string $class
         * @return object
         */
        public static function GetCurrentUser( $class ) {
            $user = Session::getParameter( $class );

            return $user;
        }


        /**
         * Login User
         *
         * @param object $user
         * @param string $class
         */
        public static function Login( $user, $class ) {
            Cookie::setCookie(  $class . "[login]",    $user->login,    time() + self::LoginCookieLifeTime );
            Cookie::setCookie(  $class . "[password]", $user->password, time() + self::LoginCookieLifeTime );

            Session::setParameter( $class,           $user );
            Session::setParameter( $class . "Logged", true);
        }


        /**
         * Logout User
         *
         * @param string $class
         */
        public static function Logout( $class ) {
            Cookie::setCookie(  $class . "[login]",    "", time() - 1024 );
            Cookie::setCookie(  $class . "[password]", "", time() - 1024 );

            Session::setParameter( $class,            null );
            Session::setParameter( $class . "Logged", false);
        }


        /**
         * Set Variables To Response
         *
         * @param object $user
         * @param string $class
         */
        public static function ToResponse( $user, $class ) {
            Response::setObject( "__" . $class, $user );
            Response::setBoolean( "__" . $class . "Logged", true );
        }


        /**
         * Generate random strong password
         * @static
         * @param int  $pw_length    password length
         * @param bool $use_caps     use caps
         * @param bool $use_numeric  use numeric
         * @param bool $use_specials use special
         * @return string
         */
        public static function GeneratePassword( $pw_length = 8, $use_caps = true, $use_numeric = true, $use_specials = true ) {
            $caps         = array();
            $numbers      = array();
            $num_specials = 0;
            $reg_length   = $pw_length;
            $pws          = array();
            $chars        = array();

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

                $signs = array();
                for ( $si = 33; $si <= 47; $si++ ) {
                    $signs[] = $si;
                }
                $rs_keys = array_rand( $signs, $num_specials );
                foreach ( $rs_keys as $rs ) {
                    $pws[] = chr( $signs[$rs] );
                }
            }
            $rand_keys = array_rand( $all, $reg_length );
            $pw        = array();
            foreach ( $rand_keys as $rand ) {
                $pw[] = chr( $all[$rand] );
            }
            $compl = array_merge( $pw, $pws );
            shuffle( $compl );
            return implode( '', $compl );
        }
    }
?>