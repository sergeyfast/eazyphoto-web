<?php
    define( 'SPAN_YEARS', 0 );
    define( 'SPAN_MONTHES', 1 );
    define( 'SPAN_WEEKS', 2 );
    define( 'SPAN_DAYS', 3 );
    define( 'SPAN_HOURS', 4 );
    define( 'SPAN_MINUTES', 5 );
    define( 'SPAN_SECONDS', 6 );

    /**
     * DateTime Helper
     *
     * @package Eaze
     * @subpackage Helpers
     */
    class DateTimeHelper {

        /**
         * Format Containers for strftime
         * Linux =  2 марта
         * FreeBSD =  2 марта
         * Windows = 02 мар
         * @var string[]
         */
        public static $RelativeDatesFormat = array(
            Package::OsFreeBSD   => '%e %B'
            , Package::OsLinux   => '%e %b'
            , Package::OsWindows => '%d %b'
        );


        /**
         * Translations
         * @var array
         */
        public static $keys = array(
            0 => array(
                1 => 'vt.common.year'
                , 2 => 'vt.common.years'
                , 5 => 'vt.common.manyYears'
            )
            , 1 => array(
                1   => 'vt.common.month'
                , 2 => 'vt.common.monthes'
                , 5 => 'vt.common.manyMonthes'
            )
            , 2 => array(
                1 => 'vt.common.week'
                , 2 => 'vt.common.weeks'
                , 5 => 'vt.common.manyWeeks'
            )
            , 3 => array(
                1   => 'vt.common.day'
                , 2 => 'vt.common.days'
                , 5 => 'vt.common.manyDays'
            )
            , 4 => array(
                1   => 'vt.common.hour'
                , 2 => 'vt.common.hours'
                , 5 => 'vt.common.manyHours'
            )
            , 5 => array(
                1   => 'vt.common.minute'
                , 2 => 'vt.common.minutes'
                , 5 => 'vt.common.manyMinutes'
            )
            , 6 => array(
                1   => 'vt.common.second'
                , 2 => 'vt.common.seconds'
                , 5 => 'vt.common.manySeconds'
            )
        );

        public static $secs = array(
            0   => 31557600 
            , 1 => 2592000
            , 2 => 604800
            , 3 => 86400
            , 4 => 3600
            , 5 => 60
            , 6 => 1
        );


        /**
         * Custom Div (via gmp if exists)
         * @param $a
         * @param $b
         * @return int|string
         */
        private static function customDiv( $a, $b ) {
			if (function_exists('gmp_div')) {
                return gmp_strval( gmp_div( $a, $b ) );
			}

            return (int)($a / $b);
		}


        /**
         * Custom Mod (via gmp if exists)
         * @param $a
         * @param $b
         * @return int|string
         */
        private static function customMod( $a, $b ) {
			if (function_exists('gmp_mod')) {
				return gmp_strval(gmp_mod($a, $b));
			}

            return $a % $b;
		}


        /**
         * Gets interval string
         *
         * @param int   $seconds  Interval time in seconds.
         * @param int   $maxSpan
         * @param array $allowedSpans
         * @return string
         */
        public static function GetIntervalString( $seconds, $maxSpan = SPAN_DAYS, $allowedSpans = array( 0, 1, 2, 3, 4, 5, 6 ) ) {
            $cases  = array (2, 0, 1, 1, 1, 2);
            $date   = self::GetIntervalArray( $seconds, $maxSpan );
            $result = "";

            foreach ( $date as $key => $value ) {
                if ( !in_array($key, $allowedSpans ) ) {
                    continue;
                }


                $val = Convert::ToInt( $value );
                if ( !(empty( $val ) ) ) {
                    $form = ($val%100>4 && $val%100<20)? 2 : $cases[min($val%10, 5)];
                    
                    switch ( $form ) {
                        case 0:
                            $count = 1;
                            break;
                        case 1:
                            $count = 2;
                            break;
                        case 2:
                            $count = 5;
                            break;
                    }

                    $result .= " " . $value . " " . LocaleLoader::Translate( self::$keys[$key][$count] );
                }
            }

            return $result;
        }


        /**
         * Gets the array of time intervals such as weeks, days, minutes and etc.
         *
         * @param int $seconds
         * @param int $maxSpan
         * @return array
         */
        public static function GetIntervalArray( $seconds, $maxSpan = SPAN_DAYS ) {
            $val    = $seconds;
            $return = array();

            for ( $i = $maxSpan; !empty( self::$secs[$i] ); $i++ ) {
                if ( $i == SPAN_WEEKS ) {
                    continue;
                }

                $return[$i] = self::customDiv( $val, self::$secs[$i] );
                $val        = self::customMod( $val, self::$secs[$i] );
            }

            return $return;
        }

        /**
         * Represents given interval as specified time span
         *
         * @param long $seconds
         * @param int $span
         * @return int
         */
        public static function GetInterval( $seconds, $span = SPAN_DAYS ) {
            if ( empty( self::$secs[$span] ) ) {
                return null;
            }

            $secs = self::$secs[$span];

            $result = self::customMod($seconds, $secs);

            return $result;
        }


        /**
         * Is Today
         *
         * @param DateTime $date
         * @return bool
         */
        public static function IsToday( $date ) {
            if ( !$date ) {
                return false;
            }

            /** Check For Today */
            return ( $date->format( 'd.m.Y' ) == date( 'd.m.Y' ) );
        }


        /**
         * Get Relative Date String
         * @param DateTime $date  the date object
         * @param bool     $useTimeInsteadOfToday
         * @return null|string
         */
        public static function GetRelativeDateString( $date, $useTimeInsteadOfToday = true ) {
            if ( !$date ) {
                return null;
            }

            /** Check For Today */
            if ( self::IsToday( $date ) ) {
                if ( $useTimeInsteadOfToday ) {
                    return $date->format('G:i');
                } else {
                    return LocaleLoader::Translate('vt.common.today');
                }
            }

            /** Check Month */
            if ( $date->format('Y') == date( 'Y' ) ) {
                return Package::$CurrentOS ? strftime( self::$RelativeDatesFormat[Package::$CurrentOS], $date->format( 'U' ) ) : $date->format( 'd.m' );
            }

            /** Check Year */
            return $date->format( "d.m.Y" );
        }


        /**
         * ConvertTimeToSeconds
         *
         * @param string $timeString (ex. "02:30:00")
         * @return int
         */
        public static function ConvertTimeToSeconds( $timeString ) {
            if ( empty( $timeString ) ) {
                return 0;
            }

            $values   = explode(':', $timeString);
            
            return $values[2] + 60 * $values[1] + 3600 * $values[0];
        }


        /**
         * Check Intersection of two date ranges
         * @static
         * @param DateTime $r1Start first range start date
         * @param DateTime $r1End first range end date
         * @param DateTime $r2Start second range start date
         * @param DateTime $r2End second range end date
         * @return bool
         */
        public static function Intersects( DateTime $r1Start, DateTime $r1End, DateTime $r2Start, DateTime $r2End)
        {
            return ( $r1Start == $r2Start ) || ( $r1Start > $r2Start ? $r1Start <= $r2End : $r2Start <= $r1End);
        }
    }
?>