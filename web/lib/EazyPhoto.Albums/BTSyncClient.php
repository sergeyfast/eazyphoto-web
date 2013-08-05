<?php
    /**
     * BitTorrent Sync Client
     * @author sergeyfast
     * @version 1.0
     *
     * Not Implemented Methods:
     *  setSettings
     *  setFolderPreferences
     */
    class BTSyncClient {

        /**
         * Use Objects in Result
         * @var bool
         */
        public $UseObjectsInResults = false;

        /**
         * Current Token
         * @var string
         */
        protected $token;

        /**
         * Curl Options
         * @var array
         */
        public $CurlOptions = array(
            CURLOPT_RETURNTRANSFER => 1
        );

        /**
         * Cookie.txt
         * @var string
         */
        public $CookieFile = 'cookie.txt';


        /**
         * Create New BitTorrent SyncApp client
         * @param string $serverUrl     like http://127.0.0.1:8888/
         * @param string $user
         * @param string $password
         * @return self
         */
        public function __construct( $serverUrl = 'http://127.0.0.1:8888', $user = null, $password = null ) {
            $this->CurlOptions[CURLOPT_URL]        = trim( $serverUrl, '/' );
            $this->CurlOptions[CURLOPT_COOKIEFILE] = $this->CookieFile;
            $this->CurlOptions[CURLOPT_COOKIEJAR]  = $this->CookieFile;

            if ( $user && $password ) {
                $this->CurlOptions[CURLOPT_USERPWD] = sprintf( '%s:%s', $user, $password );
            }
        }


        /**
         * Process Call
         * @param array  $params get parameters
         * @param bool   $rawData
         * @param string $url
         * @return array|object|bool
         */
        protected function processCall( $params = array(), $rawData = false, $url = '/gui/' ) {
            $options = $this->CurlOptions;
            $params  = $params ? : array();

            $params['t'] = time();
            if ( $this->token ) {
                $params['token'] = $this->token;
            }

            $options[CURLOPT_URL] .= sprintf( '%s?%s', $url, http_build_query( $params ) );
            $ch = curl_init();
            curl_setopt_array( $ch, $options );
            $data = curl_exec( $ch );
            $data = $rawData ? $data : json_decode( $data, !$this->UseObjectsInResults );
            curl_close( $ch );

            if ( $data === null ) {
                return false;
            }

            return $data;
        }


        /**
         * Request Token
         * @return bool
         */
        public function RequestToken() {
            $token = $this->processCall( array(), true, '/gui/token.html' );
            if ( $token ) {
                $this->token = strip_tags( $token );
                return true;
            }

            return false;
        }


        /**
         * Get Os Type
         * @return array os
         */
        public function GetOsType() {
            return $this->processCall( array( 'action' => 'getostype' ) );
        }


        /**
         * Get Version
         * @return string
         */
        public function GetVersion() {
            $version = $this->processCall( array( 'action' => 'getversion' ) );
            if ( $version ) {
                $v = $version['version'];
                return sprintf( '%d.%d.%d', ( $v & 0xFF000000 ) >> 24, ( $v & 0x00FF0000 ) >> 16, $v & 0x0000FFFF );
            }

            return false;
        }


        /**
         * Generate Secret
         * @return array rosecret,secret
         */
        public function GenerateSecret() {
            return $this->processCall( array( 'action' => 'generatesecret' ) );
        }


        /**
         * Get Settings
         * @return array settings[devicename,dlrate,listeningport,portmapping,ulrate]
         */
        public function GetSettings() {
            return $this->processCall( array( 'action' => 'getsettings' ) );
        }


        /**
         * Get Sync Folders
         * @return array folders[.. [name, peers ..[direct, name, status], secret, size] ], speed
         */
        public function GetSyncFolders() {
            return $this->processCall( array( 'action' => 'getsyncfolders' ) );
        }


        /**
         * Check New Version
         * @return array version[url,version]
         */
        public function CheckNewVersion() {
            return $this->processCall( array( 'action' => 'checknewversion' ) );
        }


        /**
         * Get Folder Preferences
         * @param string $path
         * @param string $secret
         * @return array folderpref[deletetotrash,iswritable,relay,searchdht,searchlan,usehosts,usetracker,readonlysecret]
         *
         */
        public function GetFolderPreferences( $path, $secret ) {
            return $this->processCall( array( 'action' => 'getfolderpref', 'name' => $path, 'secret' => $secret ) );
        }


        /**
         * Get Hosts
         * @param string $path
         * @param string $secret
         * @return array
         */
        public function GetHosts( $path, $secret ) {
            return $this->processCall( array( 'action' => 'getknownhosts', 'name' => $path, 'secret' => $secret ) );
        }


        /**
         * Get Dir from Server
         * @param string $path /
         * @return array folders
         */
        public function GetDir( $path = '' ) {
            return $this->processCall( array( 'action' => 'getdir', 'dir' => $path ) );
        }


        /**
         * Add Host
         * @param string $path
         * @param string $secret
         * @param string $addr
         * @param int    $port
         * @return array
         */
        public function AddHost( $path, $secret, $addr, $port ) {
            return $this->processCall( array( 'action' => 'addknownhosts', 'name' => $path, 'secret' => $secret, 'addr' => $addr, 'port' => $port ) );
        }


        /**
         * Remove Host
         * @param string $path
         * @param string $secret
         * @param int    $index
         * @return array
         */
        public function RemoveHost( $path, $secret, $index ) {
            return $this->processCall( array( 'action' => 'removeknownhosts', 'name' => $path, 'secret' => $secret, 'index' => $index ) );
        }


        /**
         * Update Secret
         * @param string $path
         * @param string $secret
         * @param string $newSecret
         * @return array
         */
        public function UpdateSecret( $path, $secret, $newSecret ) {
            return $this->processCall( array( 'action' => 'updatesecret', 'name' => $path, 'secret' => $secret, 'newsecret' => $newSecret ) );
        }


        /**
         * Generate Invite
         * @param string $path
         * @param string $secret
         * @return array
         */
        public function GenerateInvite( $path, $secret ) {
            return $this->processCall( array( 'action' => 'generateinvite', 'name' => $path, 'secret' => $secret ) );
        }


        /**
         * Generate Read Only Invite
         * @param string $path
         * @param string $secret
         * @return array
         */
        public function GenerateReadOnlyInvite( $path, $secret ) {
            return $this->processCall( array( 'action' => 'generateroinvite', 'name' => $path, 'secret' => $secret ) );
        }


        /**
         * Add Sync Folder
         * @param string $path
         * @param string $secret
         * @param bool   $force
         * @return array error,  + message,n,secret
         */
        public function AddSyncFolder( $path, $secret, $force = false ) {
            return $this->processCall( array( 'action' => 'addsyncfolder', 'name' => $path, 'secret' => $secret, 'force' => (int) $force ) );
        }


        /**
         * Remove Sync Folder
         * @param string $path
         * @param string $secret
         * @return array error,  + message,n,secret
         */
        public function RemoveSyncFolder( $path, $secret ) {
            return $this->processCall( array( 'action' => 'removefolder', 'name' => $path, 'secret' => $secret ) );
        }

    }

?>