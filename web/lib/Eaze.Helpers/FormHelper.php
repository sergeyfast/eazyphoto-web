<?php
    /**
     * Form Helper (next HtmlHelper)
     * @since 1.2
     * @package Eaze
     * @subpackage Helpers
     * @author sergeyfast
     */
    class FormHelper {

        const Text        = 'text';
        const Password    = 'password';
        const Hidden      = 'hidden';
        const CheckBox    = 'checkbox';
        const RadioButton = 'radio';
        const File        = 'file';
        const Submit      = 'submit';
        const Button      = 'button';


        private static function createInput( $params ) {
            return '<input ' . $params . ' />';
        }

        private static function createSelect( $params, $options ) {
            return '<select ' . $params . '>' . $options . '</select>';
        }

        private static function createTextArea( $params, $value ) {
            return '<textarea ' . $params . '>' . self::RenderToForm( $value ) . '</textarea>';
        }

        private static function createLink( $params, $title ) {
            return '<a ' . $params . '>' . $title  . '</a>';
        }

        private static function generateParams( $type, $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            $params = array(
                'type'    => $type
                , 'name'  => $name
                , 'value' => $value
                , 'id'    => $controlId
                , 'class' => $class
            ) + $params;

            $result = '';
            foreach( $params as $key => $value ) {
                if ( $value === null ) {
                    continue;
                }

                if ( $key == 'value' ) {
                    $value = self::RenderToForm( $value );
                } else if ( $key == 'class' ) {
                    if ( is_array( $value ) ) {
                        $value = implode( ' ', $value );
                    }
                }

                $result .= $key . '="' . $value . '" ';
            }

            return $result;
        }


        public static function FormInput( $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            return self::createInput( self::generateParams( self::Text, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormHidden( $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            return self::createInput( self::generateParams( self::Hidden, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormPassword( $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            return self::createInput( self::generateParams( self::Password, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormFile( $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            return self::createInput( self::generateParams( self::File, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormRadioButton( $name, $value = null, $controlId = null, $class = null, $checked = false, $params = array() ) {
            if ( !empty( $checked ) ) {
                $params['checked'] = 'checked';
            }

            return self::createInput( self::generateParams( self::RadioButton, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormCheckBox( $name, $value = null, $controlId = null, $class = null, $checked = false, $params = array() ) {
            if ( !empty( $checked ) ) {
                $params['checked'] = 'checked';
            }

            return self::createInput( self::generateParams( self::CheckBox, $name, $value, $controlId, $class, $params ) );
        }


        public static function FormTextArea( $name, $value = null, $controlId = null, $class = null, $params = array() )  {
            return self::createTextArea( self::generateParams( null, $name, null, $controlId, $class, $params), $value );
        }


        public static function FormEditor( $name, $value = null, $controlId = null, $class = null, $params = array() )  {
            $editor = 'mceEditor';
            if ( !empty( $class ) ) {
                if ( is_array( $class ) ) {
                    $class[] = $editor;
                } else {
                    $class = array( $editor, $class );
                }
            } else {
                $class = $editor;
            }

            return self::createTextArea( self::generateParams( null, $name, null, $controlId, $class, $params), $value );
        }


        public static function FormSubmit( $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            return self::createInput( self::generateParams( self::Submit, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormButton( $name, $value = null, $controlId = null, $class = null, $params = array() ) {
            return self::createInput( self::generateParams( self::Button, $name, $value, $controlId, $class, $params ) );
        }

        public static function FormLink( $link, $title, $controlId = null, $class = null, $params = array() ) {
            $params['href'] = $link;

            return self::createLink( self::generateParams( null, null, null, $controlId, $class, $params ), $title );
        }



        /**
         * Render To Form
         * Convert special characters to HTML entities
         *
         * @param string $value
         * @return string
         */
        public static function RenderToForm( $value ) {
            return ( htmlspecialchars( trim( $value ) ) );
        }


        /**
         * Form DateTime
         *
         * @param string   $name
         * @param DateTime $value
         * @param string   $format
         * @param string $type
         * @return string
         */
        public static function FormDateTime( $name, $value = null, $format = 'd.m.Y G:i', $type = 'dateTime' ) {
            if ( empty( $value ) ) {
                $value = '';
            } else if ( is_object( $value ) ) {
                $value = $value->format( $format );
            }

            return self::createInput( self::generateParams( self::Hidden, $name, $value, null, 'dtpicker', array( 'rel' => $type ) ) );
        }



        public static function FormDate( $name, $value = null, $format = 'd.m.Y' ) {
            return self::FormDateTime( $name, $value, $format, 'date' );
        }

        public static function FormTime( $name, $value = null, $format = 'G:i' ) {
            return self::FormDateTime( $name, $value, $format, 'time' );
        }


        public static function FormSelect( $name, $data = null
                                        , $dataKey = null
                                        , $dataTitle = null
                                        , $value = null
                                        , $controlId = null
                                        , $class = null
                                        , $nullValue = true
                                        , $callback = null
                                        , $params = array() ) {
            $select = self::generateParams( null, $name, null, $controlId, $class, $params );
            $options = '';

            // allow null value
            if ( $nullValue ) {
                if( is_string( $nullValue ) ) {
                    $options .= sprintf( '<option value="">%s</option>', $nullValue );
                } else {
                    $options .= '<option value=""></option>';
                }
            }

            if (  !empty( $data ) ) {
                foreach ( $data as $index => $element ) {
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

                    if ( !empty( $callback ) ) {
                        $title = call_user_func_array(  $callback, array( $title, $element ) );
                    }

                    $options .= '<option value="' . $key . '"';

                    if ( !empty( $params['multiple'] ) ) {
                        $options .= ( in_array( $key, $value ) ) ? ' selected="selected">' : '>';
                    } else {
                        $options .= ( !empty($value) && $value == $key ) ? ' selected="selected">' : '>';
                    }

                    $options .=  self::RenderToForm( $title ) . '</option>';
                }
            }

            return self::createSelect( $select, $options );
        }


        public static function FormSelectMultiple( $name
                                                , $data
                                                , $dataKey = null
                                                , $dataTitle = null
                                                , $value = array()
                                                , $controlId = null
                                                , $class = null
                                                , $callback = null
                                                , $params = array() ) {
            $params['multiple'] = 'multiple';
            return self::FormSelect( $name, $data, $dataKey, $dataTitle, $value, $controlId, $class, false, $callback, $params );
        }
    }
?>
