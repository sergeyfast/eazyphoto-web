<?php
    /**
     * Get Memcached Info
     * 
     * @package Eaze
     * @subpackage Eaze.Helpers
     */
    class GetMemcachedInfo {

        /**
         * Load All Packages in the system
         */
        private function loadPackages() {
            $packageDir = DirectoryInfo::GetInstance(__LIB__);
            $packages   = $packageDir->getAll( 0, 100 );
            foreach( $packages as $package ) {
                Package::Load($package["fullName"]);
            }
        }


        /**
         * Get All Tables
         */
        private function getTags() {
            $tags    = array();
            $this->loadPackages();

            $classes = get_declared_classes();
            foreach( $classes as $class ) {
                if ( strpos($class, "Factory") !== false ) {
                    $vars = get_class_vars($class);
                    if( !empty( $vars["mapping"]["flags"]["CanCache"] )
                        && !empty($vars["mapping"]["table"]))
                    {
                        $tags[] = $vars["mapping"]["table"];
                    }
                }
            }
            sort($tags);

            
            return $tags;
        }


        /**
         * Get Tag Versions
         * @param array $tags
         */
        private function getTagVersions( $tags ) {
            $result    = array();
            $mTags     = MemcacheHelper::Get($tags);
            foreach( $tags as $tag ) {
                $mTagKey = MemcacheHelper::PrepareKey( $tag );
                if ( !empty( $mTags[$mTagKey] ) )  {
                    $result[$tag] = $mTags[$mTagKey];
                } else {
                    $result[$tag] = null;
                }
            }

            return $result;
        }


        /**
         * Increment Tag
         * @param  $inc  the tag name
         * @return bool
         */
        private function incrementTag( $inc ) {
            $version = MemcacheHelper::Get( $inc );
            if ( empty( $version ) ) {
                $version = 1;
            } else {
                $version ++;
            }
                        
            return MemcacheHelper::Set( $inc, $version );
        }


        /**
         * Execute
         * @return string   the redirect name or null
         */
        public function Execute() {
            if ( !MemcacheHelper::IsActive() ) {
                return null;
            }

            $inc = Request::getString( 'inc' );
            $this->incrementTag( $inc );
            
            $flush = Request::getInteger("flush");
            if ( $flush == 1 ) {
                MemcacheHelper::Flush();
                return "success";
            }
            
            $result = MemcacheHelper::GetStats();

            $percCacheHit  =((real)$result ["get_hits"] / (real) $result ["cmd_get"] *100);
            $percCacheHit  = round($percCacheHit,2);
            $percCacheMiss = 100 - $percCacheHit;

            $result["percCacheHit"]  = $percCacheHit;
            $result["percCacheMiss"] = $percCacheMiss;
            $result["tags"]          = $this->getTagVersions($this->getTags());

            Response::setArray( "result", $result );

            return null;
        }
    }
?>