<?php
    Package::Load( 'GoKP.System' );

    /**
     * VtHelper
     * @package    10K
     * @subpackage System
     * @author     Sergeyfast
     */
    class VtHelper {


        /**
         * Get Hint
         * @static
         * @param $hint
         * @param bool $translate
         * @return string
         */
        public static function GetHint( $hint, $translate = true ) {
            $hint = trim( $hint );
            if ( $translate ) {
                $hint = LocaleLoader::Translate( $hint );
            }

            if ( empty( $hint ) ) {
                return '';
            }

            $xhtml = <<<html
            <div class="hint">
                <a href="#" class="hint-icon">?</a>
                <div class="hint-text" style="display:none;">
                    <span>?</span> {$hint}
                </div>
            </div>
html;
            return $xhtml;
        }



        /**
         * Get Bool Template
         *
         * @param $bool bool  The bool Value
         * @return string
         */
        public static function GetBoolTemplate( $bool = false ) {
            if ( $bool ) {
                return sprintf( '<span class="status green" title="%s">%s</span>', 'Да', 'Да' );
            } else {
                return sprintf( '<span class="status" title="%s">%s</span>', 'Нет', 'Нет');
            }
        }


        /**
         * Get True Bool Template
         * @static
         * @param bool $bool  bool result
         * @param string $hint  hint message
         * @param bool $translate translate message via LocaleLoader::Translate
         * @param string $status css class for status
         * @return string xhtml
         */
        public static function GetTrueBoolTemplate( $bool = false, $hint, $translate = true, $status = 'green' ) {
            if ( $bool ) {
                if ( $translate ) {
                    $hint = LocaleLoader::Translate( $hint );
                }
                return sprintf( '<span class="status %s" title="%s">%s</span>', $status, $hint, $hint );
            }
        }
    }

?>