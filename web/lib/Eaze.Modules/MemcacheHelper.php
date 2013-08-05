<?php
    /**
     * Memcache Helper
     * @link        http://ru2.php.net/memcache
     *
     * See $defaultParams for module params
     * Example:
     *     <memcache class="MemcacheHelper">
     *        <servers autocompress="true" active="true">
     *           <server host="127.0.0.1" active="true" />
     *           <server host="127.0.0.1" active="true" />
     *           <server host="127.0.0.1" active="false" />
     *       </servers>
     *     </memcache>
     *
     * @desc        Module Parameters
     * @package     Eaze
     * @subpackage  Modules
     * @static
     * @author      s.bykov
     * @author      m.grigoriev
     */

    class MemcacheHelper implements IModule {

        /**
         * Controls the minimum value length before attempting to compress automatically
         */
        const AutoCompressThreshold = 20000;

        /**
         * Specifies the minimum amount of savings to actually store the value compressed.
         * The supplied value must be between 0 and 1.
         * Default value is 0.2 giving a minimum 20% compression savings.
         */
        const AutoCompressMinSaving = 0.2;

        /**
         * Expiration time of the item. If it's equal to zero, the item will never expire.
         * You can also use Unix timestamp or a number of seconds starting from current time,
         * but in the latter case the number of seconds may not exceed 2592000 (30 days).
         */
        const CacheDefaultExpire = 3600;

        /**
         * Same expiration time for block keys.
         */
        const CacheKeyDefaultExpire = 30;

        /**
         * Counter for get requests.
         *
         * @var int
         */
        public static $TotalGetRequests = 0;

        /**
         * Counter for set request including add/set/replace/delete/increment/decrement methods too.
         *
         * @var int
         */
        public static $TotalSetRequests = 0;

        /**
         * Default params for servers and client. See memcache::addServer() for details
         *
         * @var array
         */
        private static $defaultParams = array(
            'server' => array(
                'host'              => '127.0.0.1'
                , 'port'            => 11211
                , 'active'          => false
                , 'persistent'      => false
                , 'weight'          => 1
                , 'timeout'         => 1
                , 'retryInterval'   => 15
                , 'status'          => true
                , 'failureCallback' => 'MemcacheHelper::FailureCallback'
            )
            , 'client' => array(
                'autocompress' => false
                , 'compress'   => false
                , 'active'     => false
                , 'hostKey'    => null
            )
        );

        /**
         * Servers params
         *
         * @var array
         */
        private static $serversParams = array();

        /**
         * Client params
         * @var array
         */
        private static $clientParams = array();

        /**
         * Memcache connection state
         *
         * @var bool
         */
        private static $isActive = false;

        /**
         * Initialized flag
         *
         * @var bool
         */
        private static $initialized = false;

        /**
         * Memcache instance
         *
         * @var Memcache
         */
        private static $memcache;

        /**
         * Key prefix
         *
         * @var null|string
         */
        private static $keyPrefix;


        /**
         * Module initialization.
         *
         * @static
         * @param DOMNodeList $params
         * @return null
         */
        public static function Init( DOMNodeList $params ) {
            $serversNode = $params->item( 0 );

            foreach ( $serversNode->attributes as $attribute ) {
                self::$clientParams [$attribute->name] = $attribute->value;
            }

            /** @var $serverDOMElement DOMElement */
            /** @var $serverAttribute DOMAttr */
            foreach ( $serversNode->childNodes as $serverDOMElement ) {
                $server = array();
                if ( !empty( $serverDOMElement->attributes ) ) {
                    foreach ( $serverDOMElement->attributes as $serverAttribute ) {
                        $server[$serverAttribute->name] = $serverAttribute->value;
                    }
                }

                self::$serversParams[] = $server + self::$defaultParams['server'];
            }

            if ( empty( self::$clientParams['active'] ) || self::$clientParams['active'] == 'false' ) {
                self::$isActive = false;
                Logger::Info( 'Memcache support is disabled by the sites.xml' );
            } else {
                self::$isActive = true;

                if ( !class_exists( 'Memcache' ) ) {
                    self::$isActive = false;
                    Logger::Warning( 'Memcache module is not installed' );
                }
            }

            self::$initialized = true;
            self::$keyPrefix   = !empty( self::$clientParams['hostKey'] ) ? self::$clientParams['hostKey'] : sprintf( '%s_', substr( Host::GetCurrentKey(), 0, 5 ) );

            self::connect();

            return null;
        }


        /**
         * Getter for memcache connection state.
         *
         * @static
         * @return bool
         */
        public static function IsActive() {
            return self::$isActive;
        }


        /**
         * Getter for key prefix.
         *
         * @static
         * @return string
         */
        public static function GetKeyPrefix() {
            return self::$keyPrefix;
        }


        /**
         * Callback error handler for memcache.
         *
         * @static
         * @param string $host
         * @param int    $tcpPort
         * @param int    $udpPort
         * @param int    $errorNumber
         * @param string $errorMessage
         */
        public static function FailureCallback( $host = null, $tcpPort = 0, $udpPort = 0, $errorNumber = 0, $errorMessage = null ) {
            Logger::Warning( 'Memcache %s:%d (%d) failed with %d:%s', $host, $tcpPort, $udpPort, $errorNumber, $errorMessage );
        }


        /**
         * Prepare key for memcache.
         *
         *
         * @static
         * @param string $key
         * @return string
         */
        public static function PrepareKey( $key ) {
            return sprintf( '%s%s', self::GetKeyPrefix(), md5( $key ) );
        }


        /**
         * Get value from the server.
         *
         * @param  string|array  $key  key parameter
         * @param bool           $prepareKey
         * @return mixed
         */
        public static function Get( $key, $prepareKey = true ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }
            self::$TotalGetRequests++;
            Logger::Debug( 'Get value with key %s', is_array( $key ) ? implode( '; ', $key ) : $key );

            if ( $prepareKey ) {
                if ( is_array( $key ) ) {
                    foreach ( $key as &$k ) {
                        $k = self::PrepareKey( $k );
                    }
                } else {
                    $key = self::PrepareKey( $key );
                }
            }

            return self::$memcache->get( $key );
        }


        /**
         *  Set item to the server (add|set|replace).
         *
         * @param string      $operation   add, set or replace operation
         * @param string      $key         key parameter
         * @param string      $value       value
         * @param int         $flag        Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib). Default 0
         * @param int         $expire      expiration date, default is 3600*
         * @param bool        $prepareKey
         * @return bool
         */
        private static function setValue( $operation, $key, $value, $flag = 0, $expire = self::CacheDefaultExpire, $prepareKey = true ) {
            if ( !in_array( $operation, array( 'add', 'set', 'replace') ) || !self::IsActive() || empty( $key ) ) {
                return false;
            }

            self::$TotalSetRequests++;
            $flag = self::checkCompressCompatibility( $value ) && $flag == MEMCACHE_COMPRESSED ? MEMCACHE_COMPRESSED : 0;

            if ( $prepareKey ) {
                $key = self::PrepareKey( $key );
            }

            Logger::Debug( '%s value with key %s, flag %d, expire %d', $operation, $key, $flag, $expire );
            return self::$memcache->$operation( $key, $value, $flag, $expire );
        }


        /**
         *  Add an item to the server.
         *
         * @param string      $key         key parameter
         * @param string      $value       value
         * @param int         $flag        Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib). Default 0
         * @param int         $expire      expiration date, default is 3600*
         * @param bool        $prepareKey
         * @return bool
         */
        public static function AddValue( $key, $value, $flag = 0, $expire = self::CacheDefaultExpire, $prepareKey = true ) {
            return self::setValue( 'add', $key, $value, $flag, $expire, $prepareKey );
        }


        /**
         * Set value
         *
         * @param string   $key     key parameter
         * @param string   $value   value
         * @param int      $flag    Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib). Default 0
         * @param int      $expire  expiration date, default is 3600
         * @param bool     $prepareKey
         * @return mixed
         */
        public static function Set( $key, $value, $flag = 0, $expire = self::CacheDefaultExpire, $prepareKey = true ) {
            return self::setValue( 'set', $key, $value, $flag, $expire, $prepareKey );
        }


        /**
         * Replace value
         *
         * @param  string  $key     key parameter
         * @param  string  $value   value
         * @param  int     $flag    Use MEMCACHE_COMPRESSED to store the item compressed (uses zlib). Default 0
         * @param  int     $expire  expiration date, default is 3600
         * @param bool     $prepareKey
         * @return bool
         */
        public static function Replace( $key, $value, $flag = 0, $expire = self::CacheDefaultExpire, $prepareKey = true ) {
            return self::setValue( 'replace', $key, $value, $flag, $expire, $prepareKey );
        }


        /**
         * Increment value by key
         *
         * @static
         * @param string $key key parameter
         * @param bool   $prepareKey
         * @return int|false
         */
        public static function Increment( $key, $prepareKey = true ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }
            self::$TotalSetRequests++;

            if ( $prepareKey ) {
                $key = self::PrepareKey( $key );
            }

            Logger::Debug( 'Increment with key %s', $key );
            return self::$memcache->increment( $key );
        }


        /**
         * Decrement value by key.
         *
         * @static
         * @param string $key       key parameter
         * @param bool   $prepareKey
         * @return int|false
         */
        public static function Decrement( $key, $prepareKey = true ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }
            self::$TotalSetRequests++;

            if ( $prepareKey ) {
                $key = self::PrepareKey( $key );
            }

            Logger::Debug( 'Decrement with key %s', $key );
            return self::$memcache->decrement( $key );
        }


        /**
         * Try to add block key, if block key already exists return false.
         *
         * @static
         * @param  string     $key     key parameter
         * @param  int        $value   value
         * @param  int        $expire  expiration date, default is 3600
         * @return bool|void
         */
        public static function AddBlock( $key, $value = 1, $expire = self::CacheKeyDefaultExpire ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }

            $blockKey = sprintf( '%s_block', self::PrepareKey( $key ) );
            return self::$memcache->add( $blockKey, $value, 0, $expire  );
        }


        /**
         * Getting blocking key status.
         *
         * @static
         * @param  string  $key  key parameter
         * @return bool
         */
        public static function IsBlocked( $key ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }

            $blockKey = sprintf( '%s_block', self::PrepareKey( $key ) );
            $result   = self::$memcache->get( $blockKey );

            return ( $result !== false ) ? true : false;
        }


        /**
         * Delete blocking key.
         *
         * @static
         * @param  string      $key  key parameter
         * @return bool
         */
        public static function DeleteBlock( $key ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }

            $blockKey = sprintf( '%s_block', self::PrepareKey( $key ) );
            return self::$memcache->delete( $blockKey );
        }


        /**
         * Delete Value By Key
         *
         * @param  string     $key
         * @param  int|string $timeout Execution time of the item. If it's equal to zero, the item will be deleted right away whereas if you set it to 30, the item will be deleted in 30 seconds.
         * @return bool
         */
        public static function Delete( $key, $timeout = 0 ) {
            if ( !self::IsActive() || empty( $key ) ) {
                return false;
            }

            self::$TotalSetRequests++;

            Logger::Debug( 'Delete with key %s, timeout: %d', $key, $timeout );
            return self::$memcache->delete( self::PrepareKey( $key ), $timeout );
        }


        /**
         * Flush All Keys.
         *
         * @return bool
         */
        public static function Flush() {
            if ( !self::IsActive() ) {
                return false;
            }

            Logger::Debug( 'Flushing' );
            return self::$memcache->flush();
        }


        /**
         * Get memcache version.
         *
         * @static
         * @return string
         */
        public static function GetVersion() {
            if ( !self::IsActive() ) {
                return false;
            }

            return self::$memcache->getVersion();
        }


        /**
         * Get server stats.
         *
         * @return array
         */
        public static function GetStats() {
            if ( !self::IsActive() ) {
                return false;
            }

            return self::$memcache->getStats();
        }


        /**
         * Close memcache connection.
         *
         * @static
         * @return bool
         */
        public static function Close() {
            if ( !self::IsActive() ) {
                return false;
            }

            return self::$memcache->close();
        }


        /**
         * Compare Tag Versions.
         *
         * @param  array $tags1
         * @param  array $tags2
         * @return bool  true if equals
         */
        public static function CompareTags( $tags1, $tags2 ) {
            if ( count( $tags1 ) != count( $tags2 ) ) {
                return false;
            }

            $tags1 = empty( $tags1 ) ? array() : $tags1;
            $tags2 = empty( $tags2 ) ? array() : $tags2;

            foreach ( $tags1 as $tag1Key => $tag1Value ) {
                if ( empty( $tags2[$tag1Key] ) || $tags2[$tag1Key] != $tag1Value ) {
                    return false;
                }
            }

            return true;
        }


        /**
         * Add memcache server to the connection pool.
         *
         * @static
         * @return bool
         */
        private static function connect() {
            if ( !self::IsActive() ) {
                return false;
            }

            self::$memcache = new Memcache();
            self::$isActive = false;

            foreach ( self::$serversParams as $server ) {
                if ( $server['active'] == 'true' || $server['active'] === true ) {
                    $isAdded = self::$memcache->addServer(
                        $server['host']
                        , $server['port']
                        , $server['persistent']
                        , $server['weight']
                        , $server['timeout']
                        , $server['retryInterval']
                        , $server['status']
                        , $server['failureCallback']
                    );

                    self::$isActive = $isAdded === true ? true : self::$isActive;
                }
            }

            if ( self::IsActive() ) {
                if ( !empty( self::$clientParams['autocompress'] ) || self::$clientParams['active'] != 'false' ) {
                    self::$memcache->setCompressThreshold( self::AutoCompressThreshold, self::AutoCompressMinSaving );
                }
            } else {
                Logger::Warning( 'All memcache servers were marked as an inactive' );
            }

            return true;
        }


        /**
         * Check data type. If data has type boolean, integer or float
         * compressing is not supported.
         *
         * @static
         * @param  mixed $value  value
         * @return bool
         */
        private static function checkCompressCompatibility( $value ) {
            return !( !self::$clientParams['compress'] || is_bool( $value ) || is_int( $value ) || is_float( $value ) );
        }

    }
?>