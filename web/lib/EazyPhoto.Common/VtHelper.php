<?php
    use Eaze\Helpers\FormHelper;


    /**
     * VtHelper
     * @author sergeyfast
     */
    class VtHelper {


        /**
         * Get Hint
         * @param string $hint
         * @param bool   $translate
         * @return string
         */
        public static function GetHint( $hint, $translate = true ) {
            $hint = trim( $hint );
            if ( $translate ) {
                $hint = T( $hint );
            }

            if ( !$hint ) {
                return '';
            }

            return sprintf( '<div class="col4"><span class="blockLabel"><span class="hoverHelp"><span>%s</span></span></span></div>', $hint );
        }


        /**
         * Get Extended Search HTML
         * Use for ex div: <div id="ExtendedSearch" class="displayNone" style="display: none;">
         * @return string
         */
        public static function GetExtendedSearchHtml() {
            return <<<html
<div class="col3 alignRight"><span data-target="#ExtendedSearch" class="linkPseudo blockLabel contentToggle"><span>Расширенный поиск</span></span></div>
html;
        }


        /**
         * Get Bool Template
         *
         * @param bool   $bool bool Value
         * @param string $t    true text
         * @param string $f    false text
         * @return string
         */
        public static function GetBoolTemplate( $bool = false, $t = 'Да', $f = 'Нет' ) {
            if ( $bool === null ) {
                return '';
            }

            if ( $bool ) {
                return sprintf( '<span class="status" title="%s">%1$s</span>', $t );
            }

            return sprintf( '<span class="status _fade" title="%s">%1$s</span>', $f );
        }


        /**
         * Get True Bool Template
         * @static
         * @param bool   $bool      bool result
         * @param string $hint      hint message
         * @param bool   $translate translate message via LocaleLoader::Translate
         * @param string $status    css class for status
         * @return string xhtml
         */
        public static function GetTrueBoolTemplate( $bool = false, $hint, $translate = true, $status = '' ) {
            if ( $bool ) {
                if ( $translate ) {
                    $hint = T( $hint );
                }

                return sprintf( '<span class="status %s" title="%s">%s</span>', $status, $hint, $hint );
            }
        }


        /**
         * Form Select via label+input radio
         * @param string   $name
         * @param array    $data
         * @param string   $dataKey
         * @param string   $dataTitle
         * @param string   $value
         * @param string   $class
         * @param bool     $nullValue
         * @param callback $callback
         * @param array    $params
         * @return string
         */
        public static function FormRadioSelect( $name, $data = null, $dataKey = null, $dataTitle = null, $value = null, $class = null, $nullValue = true, $callback = null , $params = [] ) {
            $xhtml = '';
            if ( $data ) {
                $i = 0;
                foreach ( $data as $index => $element ) {
                    $i ++;

                    if ( is_object( $element ) ) {
                        $title = $element->$dataTitle;
                        $key   = $element->$dataKey;
                    } else if ( is_array( $element ) ) {
                        $title = $element[$dataTitle];
                        $key   = $element[$dataKey];
                    } else {
                        $title = $element;
                        $key   = $index;
                    }

                    if ( $callback ) {
                        $title = call_user_func_array(  $callback, [ $title, $element ] );
                    }

                    $classes = $class ? [ $class ] : [];
                    if ( $i != count( $data ) ) {
                        $classes[] = 'marginRightBase';
                    }

                    $xhtml .= sprintf( '<label%s>%s %s</label>', $classes ? ' class="' . implode( $classes, ' ' ) . '"' : '',
                        FormHelper::FormRadioButton( $name, $key, null, $class, ($value && $value == $key) || (!$nullValue && $i == 1), $params ),
                        $title
                    );
                }
            }

            return $xhtml;
        }
    }
