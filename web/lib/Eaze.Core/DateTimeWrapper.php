<?php
    define( 'DEFAULT_TIMEZONE', date_default_timezone_get() );

    /**
     * DateTimeWrapper
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class DateTimeWrapper extends DateTime {

        /**
         * Date in ISO 8601 for serialization
         * @var string
         */
        private $timestamp;

        /**
         * Is Null (or now)
         * @var bool
         */
        private $isNull = false;


        /**
         * Construct
         *
         * @param string       $value
         * @param DateTimeZone $zone
         */
        public function __construct( $value, $zone = null ) {
            if ( is_null( $value ) || $value === false ) {
                $this->isNull = true;
            }

            if ( is_null( $zone ) || !$zone instanceof DateTimeZone ) {
                parent::__construct( $value );
            } else {
                parent::__construct( $value, $zone );
            }

            if ( !$this->getTimezone() ) {
                $this->setTimezone( new DateTimeZone( DEFAULT_TIMEZONE ) );
            }

            if ( is_null( $this->timestamp ) ) {
                $this->timestamp = $this->format( DateTime::ISO8601 );
            }
        }


        /**
         * Output DateTime as ISO 8601
         * @return string
         */
        public function __toString() {
            return $this->format( DateTime::ISO8601 );
        }


        /**
         * Sleep
         * @return array
         */
        public function __sleep() {
            $this->timestamp = $this->format( DateTime::ISO8601 );
            return array( 'timestamp' );
        }


        /**
         * Wake Up
         * @return void
         */
        public function __wakeup() {
            $this->__construct( $this->timestamp );
        }


        /**
         * Get New Copy from $this
         * @return DateTimeWrapper
         */
        public function NewCopy() {
            return new DateTimeWrapper( $this->format( DateTime::ISO8601 ) );
        }


        /**
         * Return "Now"
         *
         * @static
         * @return DateTimeWrapper
         */
        public static function Now() {
            return new DateTimeWrapper( 'now' );
        }


        /**
         * Compare To
         * @deprecated use <>= instead
         * @param DateTimeWrapper $object
         * @return int -1   This instance is less than obj.
         * @return int 0    This instance is equal to obj.
         * @return int 1    This instance is greater than obj.
         */
        public function CompareTo( $object ) {
            $instance = $this->format( "U" );
            $obj      = $object->format( "U" );

            if ( $instance < $obj ) {
                return -1;
            } else if ( $instance > $obj ) {
                return 1;
            } else {
                return 0;
            }
        }


        /**
         * Returns d.m.Y G:i
         * @param string $format
         * @return string
         */
        public function DefaultFormat( $format = 'd.m.Y G:i' ) {
            return $this->format( $format );
        }


        /**
         * Returns d.m.Y H:i
         * @param string $format
         * @return string
         */
        public function Default24hFormat( $format = 'd.m.Y H:i' ) {
            return $this->format( $format );
        }


        /**
         * Returns G:i
         * @param string $format
         * @return string
         */
        public function DefaultTimeFormat( $format = 'G:i' ) {
            return $this->format( $format );
        }


        /**
         * Returns d.m.Y
         * @param string $format
         * @return string
         */
        public function DefaultDateFormat( $format = 'd.m.Y' ) {
            return $this->format( $format );
        }


        /**
         * Checks if DateTime got null value in constructor
         * @return bool
         */
        public function IsNull() {
            return $this->isNull;
        }
        
    }
?>