<?php
    /**
     * Param Object
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class ParamObject  {
        /**
         * Parameters
         *
         * @var array
         */
        private $parameters;

        /**
         * Constructor
         *
         * @param array $parameters
         */
        public function __construct( &$parameters = array() ) {
            $this->parameters = $parameters;
        }


        /**
         * Get Value
         * @param  $key
         * @param string $type
         * @return mixed|null
         */
        public function GetValue( $key, $type = TYPE_PARAMETER ) {
            if ( isset( $this->parameters[$key] ) ) {
                return Convert::ToValue( $this->parameters[$key], $type );
            }

            return null;
        }


        public function SetValue( $key, $value, $type = TYPE_PARAMETER ) {
            $this->parameters[$key] = Convert::ToValue( $value, $type );
        }


        /**
         * Get or Set value to ParamObject
         * @param  string $mode MODE_GET or MODE_SET
         * @param  string $key  array key
         * @param  mixed $value
         * @param string $type TYPE_*
         * @return mixed|null
         */
        public function Value( $mode, $key, $value, $type = TYPE_PARAMETER ) {
            if ( $mode == MODE_GET ) {
                return $this->GetValue( $key, $type );
            }

            $this->SetValue( $key, $value, $type );
        }


        /**
         * Determines whether the ParamObject contains a specific key.
         * @param  $key
         * @return bool
         */
        public function ContainsKey( $key ) {
            return isset( $this->parameters[$key] );
        }


        /**
         * Get Parameters
         *
         * @return array
         */
        public function GetParameters() {
            return $this->parameters;
        }

        public function GetInteger( $key ) {
            return $this->GetValue( $key, TYPE_INTEGER );
        }

        public function GetBoolean( $key ) {
            return $this->GetValue( $key, TYPE_BOOLEAN );
        }

        public function GetString( $key ) {
            return $this->GetValue( $key, TYPE_STRING );
        }

        public function GetFloat( $key ) {
            return $this->GetValue( $key, TYPE_FLOAT );
        }

        public function GetArray( $key ) {
            return $this->GetValue( $key, TYPE_ARRAY );
        }

        public function GetObject( $key ) {
            return $this->GetValue( $key, TYPE_OBJECT );
        }

        public function GetParameter( $key ) {
            return $this->GetValue( $key, TYPE_PARAMETER );
        }

        public function GetDateTime( $key ) {
            return $this->GetValue( $key, TYPE_DATETIME );
        }

        public function SetInteger( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_INTEGER );
        }

        public function SetBoolean( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_BOOLEAN );
        }

        public function SetString( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_STRING );
        }

        public function SetFloat( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_FLOAT );
        }

        public function SetArray( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_ARRAY );
        }

        public function SetObject( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_OBJECT );
        }

        public function SetParameter( $key, $value ) {
            $this->SetValue( $key, $value, TYPE_PARAMETER );
        }

        public function SetDateTime( $key, $value ) {
            $this->SetValue( $key, $value,  TYPE_DATETIME );
        }
    }
?>