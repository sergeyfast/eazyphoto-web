<?php
    /**
     * Template
     *
     * @package Eaze
     * @subpackage Eaze.Site
     */
    class Template {
        /**
         * Registered Functions
         *
         * @var array
         */
        public static $Functions = array(
            "web"       => 'Site::GetWebPath("\\1") '
            , "webs"	=> 'Site::GetWebPath("\\1", "secure") '
            , "real"    => 'Site::GetRealPath("\\1") '
            , "lang"    => 'LocaleLoader::Translate("\\1") '
            , "upper"   => 'strtoupper("\\1") '
            , "lower"   => 'strtolower("\\1") '
            , "ucfirst" => 'ucfirst("\\1") '
            , "tobr"    => 'nl2br("\\1")'
            , "num"     => 'number_format( "\\1", 0, "", " " )'
            , "numf"    => 'number_format( "\\1", 2 )'
            , "numfr"   => 'number_format( "\\1", 2, ",", " " )'
            , "form"    => 'FormHelper::RenderToForm( "\\1" )'
            , "link"    => 'DBPageUtility::GetLink( "\\1" )'
        );

        private static $actFunctions = array(
            "increal" => 'include( Template::GetCachedRealPath("\\1") )'
        );


        /**
         * Parse Tempalte
         *
         * @param CacheManagerData $data
         */
        public static function Parse( CacheManagerData $data ) {
            $t = $data->data;

            self::parseVariables( $t );
            self::parseFuncWithVariables( $t );
            self::parseFunctions( $t );

            $data->data = $t;
        }


        /**
         * Render Template
         *
         * @param string $filename
         * @return string
         */
        public static function Render( $filename ) {
            Logger::Debug( 'Loading Template %s', $filename  );
            // Start buffering
            foreach ( Response::getParameters() as $_key => $_value ) {
                $$_key = $_value;
            }

            ob_start();
            /** @noinspection PhpIncludeInspection */
            require $filename ;
            $data = ob_get_contents();
            ob_clean();

            if ( AssetHelper::$PostProcess ) {
                $data = AssetHelper::PostProcess( $data );
            }

            echo $data;
        }



        /**
         * Parse Variables
         *
         * @param string $tempalteContents
         */
        private static function parseVariables(&$tempalteContents) {
            $m = array();
            if (preg_match_all('/{\\$([^{}]+)}/', $tempalteContents, $m )) {
	           foreach ( $m[1] as $variable ) {
	               $searchVar = $variable;

                   $replaceVar = str_replace( ".", "->", $variable );
	               $replaceVar = str_replace( "]", "']", $replaceVar );
	               $replaceVar = str_replace( "[", "['", $replaceVar );

	               $tempalteContents = str_replace(
                        sprintf( '{$%s}',  $searchVar ),
                        sprintf( '<?= $%s; ?>', $replaceVar),
                        $tempalteContents
                    );
	           }
            }
        }


        /**
         * Parse Functions with Variables
         *
         * @param string $tempalteContents
         */
        private static function parseFuncWithVariables(&$tempalteContents) {
            $m = array();
            if (preg_match_all('/{([^\s:]+):\\$([^{}]+)}/', $tempalteContents, $m)) {
               for( $i = 0; $i < count( $m[0] ); $i ++ ) {
                   $func = $m[1][$i];
                   $v    = $m[2][$i];

                   if ( !empty( self::$Functions[$func] ) ) {
                       $funcBody = str_replace( '"\\1"', '%s', self::$Functions[$func] );
                       $fullVar  = str_replace( '.', '->', $v );
                       $fullVar  = str_replace( "]", "']", $fullVar );
	                   $fullVar  = str_replace( "[", "['", $fullVar );
                       $fullFunc = sprintf( "<?= %s; ?>", sprintf( $funcBody, "$" . $fullVar ) );
                       $tempalteContents = str_replace( $m[0][$i], $fullFunc, $tempalteContents );
                   }

                   // dummy check
                   if ( !empty( self::$actFunctions[$func] ) ) {
                       $funcBody = str_replace( '"\\1"', '%s', self::$actFunctions[$func] );
                       $fullVar  = str_replace( '.', '->', $v );
                       $fullVar  = str_replace( "]", "']", $fullVar );
	                   $fullVar  = str_replace( "[", "['", $fullVar );
                       $fullFunc = sprintf( "<? %s; ?>", sprintf( $funcBody, "$" . $fullVar ) );

                       $tempalteContents = str_replace( $m[0][$i], $fullFunc, $tempalteContents );
                   }
               }
            }
        }


        /**
         * Parse Functions
         *
         * @param string $tempalteContents
         */
        private static function parseFunctions(&$tempalteContents) {
            foreach ( self::$Functions as $func => $phpFunc ) {
                $tempalteContents = preg_replace(
                     sprintf( "/{%s:([^{}]+)}/", $func ),
                     sprintf( "<?= %s;?>", $phpFunc ),
                     $tempalteContents );
            }

            // dummy check
            foreach ( self::$actFunctions as $func => $phpFunc ) {
                $tempalteContents = preg_replace(
                     sprintf( "/{%s:([^{}]+)}/", $func ),
                     sprintf( "<? %s;?>", $phpFunc ),
                     $tempalteContents );
            }
        }


        public static function GetCachedRealPath($path) {
            $filepath = CacheManager::GetCachedFilePath( Site::GetRealPath($path), "%s_%s.inc", array( "Template","Parse"));
            return $filepath;
        }

        public static function GetCachedPath($path) {
            $filepath = CacheManager::GetCachedFilePath( $path, "%s_%s.inc", array( "Template","Parse"));
            return $filepath;
        }


        /**
         * Load File and Render it
         *
         * @param string $path
         */
        public static function Load( $path ) {
            if ( !file_exists( $path ) ) {
                Logger::Fatal( 'No such template %s', $path );
            } else {
                Logger::Debug( 'Opening template: %s', $path );
                self::Render( Template::GetCachedPath( $path ) );
            }
        }
    }
?>