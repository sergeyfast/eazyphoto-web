<?php
   /**
    * Http Host
    *
    * @package Eaze
    * @subpackage Site
    * @author sergeyfast
    */
    class Host {

        /**
         * Protocol scheme
         *
         * @var string
         */
        private $protocol = 'http';

        /**
         * Hostname
         *
         * @var string
         */
        private $hostname;

        /**
         * localname
         *
         * @var string
         */
        private $localname;

        /**
         * Port
         *
         * @var integer
         */
        private $port = 80;

        /**
         * Webroot
         *
         * @var string
         */
        private $webroot;

        /**
         * Default
         *
         * @var boolean
         */
        private $default = false;

        /**
         * Current host
         *
         * @var Host
         */
        private static $currentHost;

        /**
         * Current Host MD5 Key
         *
         * @var string
         */
        private static $currentHostKey;

        /**
         * Get Path String
         *
         * @var string
         */
        private $pathString;


        /**
         * Overrided Paths
         *
         * @var array
         */
        public $Paths;


        /**
         * Constructor
         *
         * @param string   $protocol
         * @param string   $hostname
         * @param integer  $port
         * @param string   $webroot
         * @param boolean  $default
         * @param string   $localname
         */
        public function __construct( $protocol = null
                                    , $hostname = null
                                    , $port = null
                                    , $webroot = null
                                    , $default = false
                                    , $localname = null ) {
            $this->hostname  = $hostname;
            $this->default   = $default;
            $this->localname = $localname;

            if ( !is_null( $protocol ) ) {
                $this->protocol = $protocol;
            }

            if ( !is_null( $webroot ) ) {
                $this->webroot  = $webroot;
            }

            if ( !is_null( $port ) ) {
                $this->port     = $port;
            }

            $this->setPathString();
        }


        /**
         * To String
         *
         * @return string
         */
        public function __toString() {
            return sprintf( '%s://%s:%s/%s', $this->protocol, $this->hostname, $this->port, $this->webroot );
        }


        /**
         * Update Path String
         */
        public function setPathString() {
            if (( $this->protocol == 'http' && $this->port == '80' )
                || ( $this->protocol == 'https' && $this->port == '80' )
                || ( $this->protocol == 'https' && $this->port == '443' )) {
                $this->pathString = sprintf( '%s://%s%s', $this->protocol, $this->hostname,  (true == empty( $this->webroot)) ? '' : '/' . $this->webroot );
            } else {
                $this->pathString = sprintf( '%s://%s:%s%s', $this->protocol, $this->hostname, $this->port, (true == empty( $this->webroot)) ? '' : '/' . $this->webroot  );
            }
        }


        /**
         * Sets current host to default
         * @param $bDefault
         */
        public function SetDefault( $bDefault ) {
            $this->default = $bDefault;
        }


        /**
         * Sets local name
         *
         * @param string $localname
         */
        public function SetLocalname( $localname ) {
            $this->localname = $localname;
        }


        /**
         * Set Paths
         *
         * @param DOMNodeList $paths
         */
        public function SetPaths( DOMNodeList $paths ) {
            foreach ( $paths as $path ) {
                /** @var DOMElement $path  */
                $this->Paths[$path->getAttribute('name'). '://'] = $path->getAttribute('value');
            }
        }


        /**
         * Get Current Host
         *
         * @static
         * @return Host
         */
        public static function GetCurrentHost() {
            if ( self::$currentHost ) {
                return self::$currentHost;
            }

            // Get protocol
            $protocol = 'http';
            $hostname = '';
            $port     = isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : 80;
            $webroot  = Host::GetCurrentWebroot();

            if( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') )
            {
                $protocol = 'https';
            }

            if( isset( $_SERVER['REQUEST_URI'] ) ) {
                $script_name = $_SERVER['REQUEST_URI'];
            } else {
                $script_name = $_SERVER['PHP_SELF'];

                if( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] > ' ') {
                    $script_name .= '?' . $_SERVER['QUERY_STRING'];
                }
            }

            if ( isset( $_SERVER['HTTP_HOST'] ) ) {
                $hostname = $_SERVER['HTTP_HOST'];
                if ( strpos($hostname, ':') !== false ) {
                    $port = substr($hostname, strpos($hostname, ':')  + 1);
                }

            } else if ( isset( $_SERVER['SERVER_NAME'] ) ) {
                $hostname = $_SERVER['SERVER_NAME'];
            }


            if ( strpos($hostname, ':') !== false  ) {
                $hostname = substr($hostname, 0, strpos($hostname, ':') );
            }

            self::$currentHost    = new Host( $protocol, $hostname, $port, $webroot );
            self::$currentHostKey = md5( self::$currentHost->__toString() );

            return self::$currentHost;
        }


        /**
         * Reset Current Host
         */
        public static function ResetCurrentHost() {
            self::$currentHost    = null;
            self::$currentHostKey = null;
        }




        /**
         * Get Current Host MD5 Key
         *
         * @return string
         */
        public static function GetCurrentKey() {
            if ( empty( self::$currentHostKey ) ) {
                self::GetCurrentHost();
            }

            return self::$currentHostKey;
        }

    	/**
    	 * Get Webroot
    	 *
    	 * @static
    	 * @return string
    	 */
    	public static function GetCurrentWebroot(){
	        $filename = basename($_SERVER['SCRIPT_FILENAME']);

	        if (basename($_SERVER['SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (basename($_SERVER['PHP_SELF']) === $filename) {
                $baseUrl = $_SERVER['PHP_SELF'];
            }

            if ( empty($baseUrl) ) {
                $basePath = '';
            } else {
                if (basename( $baseUrl ) === $filename) {
                    $basePath = dirname( $baseUrl );
                } else {
                    $basePath = $baseUrl;
                }

                $basePath = rtrim($basePath, '/');
                $basePath = ltrim($basePath, '/' );
                $basePath = ltrim($basePath, '\\' );
            }

    	    return $basePath;
    	}


    	/**
    	 * Get Hostname
    	 *
    	 * @return string
    	 */
    	public function GetHostname() {
    	    return $this->hostname;
    	}

    	/**
    	 * Get Port
    	 *
    	 * @return integer
    	 */
    	public function GetPort() {
    	    return $this->port;
    	}

    	/**
    	 * Get Protocol
    	 *
    	 * @return string
    	 */
    	public function GetProtocol() {
    	    return $this->protocol;
    	}


    	/**
    	 * Get Webroot
    	 *
    	 * @return string
    	 */
    	public function GetWebroot() {
    	    return $this->webroot;
    	}


     	/**
    	 * Get Default
    	 *
    	 * @return boolean
    	 */
    	public function GetDefault() {
    	    return $this->default;
    	}


 	    /**
    	 * Get Default
    	 *
    	 * @return boolean
    	 */
    	public function GetLocalname() {
    	    return $this->localname;
    	}


    	/**
    	 * Get PathString
    	 *
    	 * @return string
    	 */
    	public function GetPathString() {
    	    return $this->pathString;
    	}
    }
?>