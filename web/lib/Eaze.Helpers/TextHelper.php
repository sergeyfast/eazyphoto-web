<?php
    /**
     * Text Helper
     *
     * @package Eaze
     * @subpackage Helpers
     * @author sergeyfast
     */
    class TextHelper {

        /**
         * Translit
         *
         * @param string $cyrString
         * @return string
         */
        public static function Translit( $cyrString ) {
            static $replacement;

            if ( empty( $replacement ) ) {
                $replacement = array(
                    'ё' => 'yo', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ь' => '', 'ю' => 'yu', 'я' => 'ya',
                    'Ё' => 'Yo', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => '', 'Ь' => '', 'Ю' => 'Yu', 'Я' => 'Ya'
                );

                $replacement += array_combine(
                    preg_split('/(?<!^)(?!$)/u', 'абвгдежзийклмнопрстуфхыэАБВГДЕЖЗИЙКЛМНОПРСТУФХЫЭ' )
                    , preg_split('/(?<!^)(?!$)/u', 'abvgdegziyklmnoprstufhieABVGDEGZIYKLMNOPRSTUFHIE' )
                );
            }

            return strtr( $cyrString, $replacement );
        }


        /**
         * Replace Cyr
         * @static
         * @param string $cyrString
         * @return string
         */
        public static function ReplaceCyr( $cyrString ) {
            $tr = array(
                'А'   => 'A', 'Е' => 'E'
                , 'К' => 'K', 'М' => 'M'
                , 'О' => 'O', 'Т' => 'T'
                , 'а' => 'a', 'е' => 'e'
                , 'к' => 'k', 'о' => 'o'
            );

            return strtr( $cyrString, $tr );
        }


        /**
         * Get First Difference
         *
         * @param string $firstString
         * @param string $secondString
         * @return array
         */
        public function FirstDifference( $firstString, $secondString ) {
            $result = array(
                'difference'  => -1
                , 'message'   => 'Empty string'
                , 'chars'     => ''
                , 'identical' => true
            );

            if ( ( true == empty( $firstString ) )
                 || ( true == empty( $secondString ) )
            ) {
                $result['identical'] = false;

                return $result;
            }

            // For
            for ( $i = 0; $i < strlen( $firstString ); $i++ ) {
                if ( strlen( $secondString ) == $i ) {
                    $result['difference'] = $i;
                    break;
                }

                if ( $firstString[$i] != $secondString[$i] ) {
                    $result['difference'] = $i;
                    $result['identical'] = false;
                    $result['chars'] = sprintf( '[%d]!=[%d]', ord( $firstString[$i] ), ord( $secondString[$i] ) );

                    break;
                }
            }

            // Check Length
            if ( strlen( $firstString ) != strlen( $secondString ) ) {
                $result['difference'] = strlen( $firstString );
                $result['identical']  = false;
                $result['message']    = 'Invalid length. Data: ' . $firstString . ' != ' . $secondString;

                return $result;
            }

            if ( $result['identical'] == false ) {
                $result['message'] = sprintf( 'First Difference in %s char: (%s). Data: %s != %s) ', $result['difference'], $result['chars'], $firstString, $secondString );
            } else {
                $result['message'] = 'Ok';
            }

            return $result;
        }


        /**
         * Convert Text To UTF-8
         * @static
         * @param string $string
         * @param string $sourceCharset
         * @return string
         */
        public static function ToUTF8( $string, $sourceCharset = 'CP1251' ) {
            return iconv( $sourceCharset, 'UTF-8', $string );
        }


        /**
         * Convert Text From UTF-8 to CP1251
         * @static
         * @param string $string
         * @param string $sourceCharset
         * @param string $destCharset
         * @return string
         */
        public static function FromUTF8( $string, $sourceCharset = 'UTF-8', $destCharset = 'CP1251' ) {
            return iconv( $sourceCharset, $destCharset, $string );
        }


        /**
         * Get Percent String
         * @static
         * @param  number $value
         * @param  number $maxValue
         * @param bool $append append % char
         * @param int $decimals
         * @param string $decPoint decimal separator
         * @return int|float|string
         */
        public static function GetPercentString( $value, $maxValue, $append = false, $decimals = 2, $decPoint = '.' ) {
            $result = 0;

            if ( $maxValue != 0 ) {
                $result = $value / $maxValue * 100;
            }

            $result = number_format( $result, $decimals, $decPoint, '' ) . (( $append ) ? '%' : '');

            return $result;
        }


        /**
         * Get Declension for Value
         * @static
         * @param float $value
         * @return int 1|2|5
         */
        public static function GetDeclension( $value ) {
            $val = Convert::ToInt( $value );
            if ( !(empty( $val ) ) ) {
                if ( $val == 1 ) {
                    $count = 1;
                } elseif( $val < 5 ) {
                    $count = 2;
                } elseif ( $val < 21 ) {
                    $count = 5;
                } else {
                    if ( $val % 10 == 1 ) {
                        $count = 1;
                    } elseif ( $val % 10 < 5 && $val % 10 != 0) {
                        $count = 2;
                    } else {
                        $count = 5;
                    }
                }

                return $count;
            }

            return 5;
        }
    }

?>