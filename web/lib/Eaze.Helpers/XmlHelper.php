<?php
    /**
     * Xml Lookup
     * @package Eaze
     * @subpackage Helpers
     * @author sergeyfast
     */
    class XmlLookup {

        /**
         * @var DOMDocument
         */
        private $doc;

        /**
         * @var DOMXPath
         */
        private $xpath;


        /**
         * Evaluate Xpath
         * @param  string $path xpath expression
         * @return DOMNodeList|mixed returns list or typed value
         */
        public function Get( $path ) {
            $result = $this->xpath->evaluate( $path );

            return $result;
        }


        /**
         * Gets single node value by XPath.
         *
         * @param string $path  XPath expression.
         * @return string
         */
        public function GetSingleValue( $path ) {
            $path = trim( $path );
            if ( empty( $path ) ) {
                return ( $this->doc->childNodes->item( 0 )->nodeValue );
            }

            $list = $this->Get( $path );

            if ( $list->length == 0 ) {
                return null;
            }

            return $list->item( 0 )->nodeValue;
        }


        /**
         * Gets array of node values selected by XPath
         *
         * @param string $path  XPath expression.
         * @return string
         */
        public function GetValues( $path ) {
            $list = $this->Get( $path );
            $result = array();

            for ( $i = 0; $i < $list->length; $i++ ) {
                $result[$list->item( $i )->nodeName] = $list->item( $i )->nodeValue;
            }

            return $result;
        }


        public function Dump( $path ) {
            $result = $this->Get( $path );

            if ( $result instanceof DOMNodeList ) {
                foreach ( $result as $node ) {
                    XmlHelper::Dump( $node );
                }
            } else {
                XmlHelper::Dump( $result );
            }
        }


        public function __construct( $node ) {
            if ( $node instanceof DOMDocument ) {
                $this->doc = $node;
            } else {
                $this->doc = new DOMDocument();
                $this->doc->preserveWhiteSpace = false;
                $this->doc->appendChild( $this->doc->importNode( $node, true ) );
            }

            $this->xpath = new DOMXPath( $this->doc );
        }
    }


    /**
     * XmlHelper
     * @package Eaze
     * @subpackage Helpers
     * @author sergeyfast
     */
    class XmlHelper {

        public static function DumpFromString( $result, $stripTags = true ) {
            $doc = new DOMDocument();
            $doc->loadXML( $result );
            $doc->formatOutput = true;
            $result = $doc->saveXML();


            if ( $stripTags ) {
                $result = htmlspecialchars( $result );
            }

            printf( "<pre>%s</pre>", $result );
        }


        /**
         * Dump DOM Element
         * @static
         * @param DOMElement $node
         * @param bool $stripTags
         * @return void
         */
        public static function Dump( DOMElement $node, $stripTags = true ) {
            $doc = new DOMDocument();
            $doc->appendChild( $doc->importNode( $node, true ) );

            $result = $doc->saveXML();

            printf( '<pre>%s</pre>', ($stripTags) ? htmlspecialchars($result) : $result );
        }


        /**
         * Get Child node by tag name
         *
         * @params string $nodeName
         * @param $nodeName
         * @param DOMNode $node
         * @return DOMNode
         */
        public static function GetChildNode( $nodeName, DOMNode $node ) {
            if ( $node->hasChildNodes() ) {
                foreach ( $node->childNodes as $childNode ) {
                    if ( $childNode->nodeName === $nodeName ) {
                        return $childNode;
                    }
                }
            }

            return null;
        }


        /**
         * Get Xml Lookup
         *
         * @param DOMElement $node
         * @return XmlLookup
         */
        public static function GetLookup( DOMElement $node ) {
            return new XmlLookup( $node );
        }


        /**
         * Merges two xml tree
         * if first has attribute and second also has it then sets
         * first elemnt attribute value as second element attribute value.
         *
         * @author Anton Lyzin
         * @param DOMNode $parent   the source tree
         * @param DOMNode $child    the second tree
         * @return mixed  result tree
         */
        public static function MergeNodes( $parent, $child ) {
            $main = new DOMDocument();

            $main->appendChild( $main->importNode( $parent, true ) );

            $rootElement = $main->childNodes->item( 0 );

            if ( $child instanceof DomText ) {
                $rootElement->deleteData( 0, strlen( $rootElement->data ) );
                $rootElement->insertData( 0, $child->data );
            }

            if ( !is_null( $child ) && !is_null( $child->childNodes ) ) {
                $i = 0;
                foreach ( $child->childNodes as $childNode ) {
                    if ( $childNode instanceof DOMComment ) {
                        continue;
                    }

                    $find = false;

                    if ( false == is_null( $rootElement->childNodes ) ) {
                        foreach ( $rootElement->childNodes as $pChildNode ) {
                            if ( $pChildNode instanceof DOMComment ) {
                                continue;
                            }

                            if ( ( $pChildNode->nodeName === $childNode->nodeName )
                                 && (
                                    ( $childNode instanceof DomText )
                                    || ( ( $pChildNode->getAttribute( "name" ) == $childNode->getAttribute( "name" ) )
                                         && ( trim( $pChildNode->getAttribute( "name" ) ) != "" )
                                         && ( trim( $childNode->getAttribute( "name" ) ) != "" ) )
                                    || ( ( $pChildNode->getAttribute( "alias" ) == $childNode->getAttribute( "alias" ) )
                                         && ( trim( $pChildNode->getAttribute( "alias" ) ) != "" )
                                         && ( trim( $childNode->getAttribute( "alias" ) ) != "" ) )
                                    || ( ( false == $childNode->hasAttribute( "name" ) )
                                         && ( false == $pChildNode->hasAttribute( "name" ) )
                                         && ( ( false == $childNode->hasAttribute( "alias" ) )
                                              && ( false == $pChildNode->hasAttribute( "alias" ) ) ) )
                                )
                            ) {
                                $find = true;

                                $element = self::MergeNodes( $pChildNode, $childNode );

                                $rootElement->replaceChild( $main->importNode( $element, true ), $pChildNode );

                                break;
                            }
                        }
                    }

                    if ( false == $find ) {
                        $main->childNodes->item( 0 )->appendChild( $main->importNode( $childNode, true ) );
                    }
                }
            }

            if ( false == is_null( $child ) && false == is_null( $child->attributes ) ) {
                // For all nodes in child
                foreach ( $child->attributes as $childNode ) {
                    if ( $rootElement->hasAttribute( $childNode->nodeName ) ) {
                        $rootElement->removeAttribute( $childNode->nodeName );
                    }

                    $rootElement->setAttribute( $childNode->nodeName, $childNode->value );
                }
            }

            // TODO add merging attributes
            $main->removeChild( $rootElement );
            $main->appendChild( $rootElement );

            return ( $main->childNodes->item( 0 ) );
        }
    }

?>