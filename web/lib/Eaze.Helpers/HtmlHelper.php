<?php
    /**
     * HTML Helper
     *
     * @package Eaze
     * @subpackage Eaze.Helpers
     */
    class HtmlHelper {

        /**
         * Render To Form
         * Convert special characters to HTML entities
         *
         * @param string $value
         * @return string
         */
        public static function RenderToForm( $value ) {
            return FormHelper::RenderToForm( $value );
        }

        /**
         * @static Generate File Upload Form control
         * @param string $controlName
         * @param string $value
         * @param string $controlId
         * @param string  $className
         * @return string
         */
        public static function FormFile( $controlName, $value = "", $controlId = null, $className = null ) {
            return FormHelper::FormFile( $controlName, $value, $controlId, $className );
        }


        /**
         * Form Text Area
         *
         * @param string  $controlName
         * @param string  $value
         * @param int $rows
         * @param int $cols
         * @param string  $controlId
         * @param null $readonly
         * @param string  $className
         * @return string
         */
        public static function FormTextArea( $controlName, $value = "", $rows = 5, $cols = 80, $controlId = null, $readonly = null, $className = null ) {
            return FormHelper::FormTextArea( $controlName, $value, $controlId, $className, array( 'rows' => $rows, 'cols' => $cols, 'readonly' => $readonly ) );
        }


        public static function FormEditor( $controlName, $value, $rows = 10, $cols = 80, $controlId = null ) {
            return FormHelper::FormEditor( $controlName, $value, $controlId, null, array( 'rows' => $rows, 'cols' => $cols ) );
        }


        /**
         * Form Checkbox
         *
         * @param string  $controlName
         * @param string $checked
         * @param integer $controlId
         * @param string  $value
         * @param string $class
         * @return string
         */
        public static function FormCheckBox( $controlName, $checked = null, $controlId = null, $value = null, $class = null ) {
            return FormHelper::FormCheckBox( $controlName, $value, $controlId, $class, $checked );
        }


        /**
         * Form RadioButton
         *
         * @param string  $controlName
         * @param string  $value
         * @param bool $checked
         * @param integer $controlId
         * @return string
         */
        public static function FormRadioButton( $controlName, $value = null, $checked = false, $controlId = null ) {
            return FormHelper::FormRadioButton( $controlName, $value, $controlId, null, $checked );
        }


        /**
         * Form Input
         *
         * @param string  $controlName
         * @param string  $value
         * @param integer $size
         * @param integer $controlId
         * @param null $class
         * @param bool $disabled
         * @return string
         */
        public static function FormInput( $controlName, $value = "", $size = 80, $controlId = null, $class = null, $disabled = false ) {
            return FormHelper::FormInput( $controlName, $value, $controlId, $class, array( 'disabled' => $disabled ? 'disabled' : null, 'size' => $size ) );
        }


        /**
         * Form Hidden
         *
         * @param string  $controlName
         * @param string  $value
         * @param string $controlId
         * @param null $className
         * @return string
         */
        public static function FormHidden( $controlName, $value = "", $controlId = null, $className = null ) {
            return FormHelper::FormHidden( $controlName, $value, $controlId, $className );
        }


        /**
         * Form Password
         *
         * @param string  $controlName
         * @param string  $value
         * @param integer $size
         * @param integer $controlId
         * @param string $class
         * @return string
         */
        public static function FormPassword( $controlName, $value = "", $size = 80, $controlId = null, $class = null ) {
            return FormHelper::FormPassword( $controlName, $value, $controlId, $class, array( 'size' => $size ) );
        }


        /**
         * Form DateTime
         *
         * @param string   $controlName
         * @param DateTime $value
         * @param string   $format
         * @param string $type
         * @return string
         */
        public static function FormDateTime( $controlName, $value = null, $format = 'd.m.Y G:i', $type = 'dateTime' ) {
            return FormHelper::FormDateTime( $controlName, $value, $format, $type );
        }



        public static function FormDate( $controlName, $value = null, $format = 'd.m.Y' ) {
            return self::FormDateTime( $controlName, $value, $format, 'date' );
        }

        public static function FormTime( $controlName, $value = null, $format = 'G:i' ) {
            return self::FormDateTime( $controlName, $value, $format, 'time' );
        }


        /**
         * Form Select Control
         *
         * @param string    $controlName
         * @param array     $data
         * @param string    $dataKey
         * @param string    $dataTitle
         * @param string    $currentId
         * @param bool      $nullValue
         * @param null $callback
         * @param null $class
         * @param null $controlId
         * @return string
         */
        public static function FormSelect( $controlName, $data = null, $dataKey = null, $dataTitle = null, $currentId = null, $nullValue = true, $callback = null, $class = null, $controlId = null ) {
            return FormHelper::FormSelect( $controlName, $data, $dataKey, $dataTitle, $currentId, $controlId, $class, $nullValue, $callback );
        }


        /**
         * Form Select Multiply Control
         *
         * @param string    $controlName
         * @param array     $data
         * @param null $dataKey
         * @param null $dataTitle
         * @param array     $selectedIds
         * @param integer   $size
         * @param null $callback
         * @param null $class
         * @param null $controlId
         * @return string
         */
        public static function FormSelectMultiple( $controlName, $data, $dataKey = null, $dataTitle = null, $selectedIds = array(), $size = 10, $callback = null, $class = null, $controlId = null ) {
            return FormHelper::FormSelectMultiple( $controlName, $data, $dataKey, $dataTitle, $selectedIds, $controlId, $class, $callback, array( 'size' => $size ) );
        }
    }
?>