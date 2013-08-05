<?php
    class GetObjectsAutocomplete {
     
        /**
        * Entry Point
        */
        public function Execute() {
            $result         = array();
            $search         = Request::getArray( 'goa_Search' );
            $options        = Request::getArray( 'goa_Options' );
            $resultFormat   = Request::getArray( 'goa_ResultFormat' );
            $resultFields   = array();

            $objectName     = Request::getString( 'goa_Object' );
            $factoryName    = $objectName . 'Factory';
            $factory        = new $factoryName;

            $objects        = $factory->Get( $search, $options );

            foreach( $resultFormat as $field => $resultFormatItem ) {
                $resultFields[$field] = array(
                    'name'      => $resultFormatItem
                    , 'isProp'  => ( property_exists( $objectName, $resultFormatItem ) ) ? true : false
                );
            }

            if( !empty( $objects ) ) {
                foreach( $objects as $object ) {
                    $resultItem = array();
                    foreach( $resultFields as $field => $resultField ) {
                        if( $resultField['isProp'] ) {
                            $resultItem[$field] = $object->$resultField['name'];
                        } else {
                            $resultItem[$field] = call_user_func( array( $object, $resultField['name'] ) );
                        }
                    }
                    $result[] = $resultItem;
                }
            }

            header( "Content-Type: text/html; charset=" . LocaleLoader::$HtmlEncoding );
            echo ObjectHelper::ToJSON( $result );
        }
    }
?>