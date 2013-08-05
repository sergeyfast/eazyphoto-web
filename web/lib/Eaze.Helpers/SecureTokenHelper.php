<?php
    /**
     * SecureToken Helper
     * Generates token from sessionId (Response, Request, JSVar name: __token)
     * @package    Eaze
     * @subpackage Helpers
     * @author     Sergeyfast
     */
    class SecureTokenHelper {

        const Variable = '__token';

        /**
         * Enabled or Disabled
         * @var bool
         */
        public static $Enabled = true;


        /**
         * Get Token (or generate new)
         * @return string
         */
        public static function Get() {
            return substr( md5( strrev( Session::getId() ) ), 3, 13 );
        }


        /**
         * Set Token to Response & Javascript (via JSHelper)
         */
        public static function Set() {
            $token = self::Get();

            JsHelper::PushLine( sprintf( "var %s = '%s'; ", self::Variable, $token ) );
            Response::setString( self::Variable, $token );
        }


        /**
         * Check Token from Request
         * @return bool
         */
        public static function Check() {
            if ( !self::$Enabled ) {
                return true;
            }

            return ( Request::getString( self::Variable ) === self::Get() );
        }


        /**
         * Form Input Hidden (token from sessionId directly)
         * @return string xhtml input type hidden
         */
        public static function FormHidden() {
            return FormHelper::FormHidden( self::Variable, self::Get() );
        }

    }

?>