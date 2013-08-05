<?php
    class CacheManagerData {
        public $data = null;

        public function __construct( $data ) {
            $this->data = $data;
        }
    }

    /**
     * Cache Manager
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class CacheManager {
        public static function GetFileDefaultChecksum( $file ) {
            return self::GetFileChecksum( $file );
        }

        public static function GetFileTimestamp( $file ) {
            return filectime( $file );
        }

        public static function GetFileChecksum( $file ) {
            return  md5_file( $file );
        }

        /**
         * Get Cached XML Path
         *
         * @param string        $fileForCache    the file for cache
         * @param string        $patternToCache  the pattern for cache
         * @param array|string  $cacheFunction   the cached function for DOMDocument
         * @return string
         */
        public static function GetCachedXMLPath( $fileForCache, $patternToCache, $cacheFunction ) {
            $checksum  = self::GetFileDefaultChecksum( $fileForCache );
            $filePath  = sprintf( '%s/' . $patternToCache, CONFPATH_CACHE, $checksum );

            if ( file_exists( $filePath ) ) {
                return $filePath;
            }

            // Move to cache
            $doc = new DOMDocument();
            if ( ! $doc->load( $fileForCache ) ) {
                Logger::Error( 'Error while loading %s', $fileForCache );
                return null;
            }

            // return cached document
            call_user_func( $cacheFunction, $doc );
            Logger::Info( '%s recompiled to %s', $fileForCache, $filePath );

            self::ClearCache( str_replace( '%s', '(.*)', $patternToCache ));
            $doc->save( $filePath );

            return $filePath;
        }


        /**
         * Get Cached File Path
         *
         * @param string        $fileForCache    the file for cache
         * @param string        $patternToCache  the pattern for cache (sites_%s.xml)
         * @param array|string  $cacheFunction   the cached function
         * @param string        $filePrefix      the file prefix
         * @return string
         */
        public static function GetCachedFilePath( $fileForCache, $patternToCache, $cacheFunction, $filePrefix = null ) {
            $checksum = self::GetFileDefaultChecksum( $fileForCache );

            if ( empty( $filePrefix ) )  {
                $pathChecksum = md5( $fileForCache ) ;
            } else {
                $pathChecksum = $filePrefix;
            }

            $filePath  = sprintf( '%s/' . $patternToCache, CONFPATH_CACHE,  $pathChecksum, $checksum  );

            if ( file_exists( $filePath ) ) {
                return $filePath;
            }

            // return cached document
            $data = new CacheManagerData( file_get_contents($fileForCache) );

            call_user_func( $cacheFunction, $data );
            Logger::Info( '%s recompiled to %s', $fileForCache, $filePath );

            self::ClearCache( str_replace( '%s', '(.*)', sprintf( $patternToCache, $pathChecksum, '%s' )));
            file_put_contents( $filePath, $data->data );

            return $filePath;
        }


        /**
         * Clear Cache
         *
         * @param string $pattern  the regexp pattern
         */
        public static function ClearCache( $pattern = '(.+)xml' ) {
            $d = dir( CONFPATH_CACHE );

            while (false !== ($entry = $d->read())) {
                if ( substr($entry, 0, 1)=='.' ) continue;

                $filename = CONFPATH_CACHE .'/'. $entry;

                if ( preg_match( '?' . $pattern . '?i' , $entry ) ) {

                    Logger::Info( $pattern );
                    unlink( $filename );
                }
            }

            $d->close();
        }
    }
?>