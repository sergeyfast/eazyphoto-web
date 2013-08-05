<?php
    if ( !defined( 'CONFPATH_SITES' ) ){
        define( 'CONFPATH_SITES', 'etc/conf/sites.xml');
    }

    if ( !defined( 'CONFPATH_ERRORS' ) ) {
        define( 'CONFPATH_ERRORS', 'etc/errors');
    }


    /**
     * SiteManager Constants
     *
     * @package Eaze
     * @subpackage Site
     */
    class SiteManagerConstants {
        const detectSiteQuery         = '//host[( (hostname="%s" or hostname="*.%s") and webroot="%s" and port="%s" and protocol="%s") or hostname="*"]';
        const xmlExtends              = 'extends';
        const xmlDevel                = 'devel';
        const xmlName                 = 'name';
        const xmlDefault              = 'default';
        const xmlPaths                = 'paths';
        const siteSettingsQuery       =  '//site[@name="%s"]/settings';
        const defaultSiteCachePattern = 'sites_%s.xml';

        public static $hostDefaults = array(
            'webroot'    => ''
            , 'port'     => 80
            , 'protocol' => 'http'
            , 'default'  => 'false'
        );
    }


    /**
     * SiteManager
     *
     * @package    Eaze
     * @subpackage Site
     * @author     sergeyfast
     */
    class SiteManager {

        /**
         * Detect Site
         * @param bool $autoDetectPage optional  start PageManager::DetectPage (default false)
         * @return void
         */
        public static function DetectSite( $autoDetectPage = true ) {
            $doc                     = new DOMDocument();
            $doc->preserveWhiteSpace = false;

            if ( !$doc->load( CacheManager::GetCachedXMLPath(
                CONFPATH_SITES, SiteManagerConstants::defaultSiteCachePattern, array( 'SiteManager', 'CacheSitesXML' ) ) )
            ) {
                Logger::Error( 'Error while loading sites.xml' );
                return;
            }

            $currentHost = Host::GetCurrentHost();
            $query       = sprintf( SiteManagerConstants::detectSiteQuery, $currentHost->GetHostname(), self::GetNextLevelDomain( $currentHost->GetHostname() ), $currentHost->GetWebroot(), $currentHost->GetPort(), $currentHost->GetProtocol() );

            Logger::Debug( 'Searching site: %s', $query );
            Logger::Checkpoint();
            $xpath = new DOMXPath( $doc );
            $host  = $xpath->query( $query )->item( 0 );

            if ( empty( $host ) ) {
                Response::HttpStatusCode( '501', 'Not Implemented' );
            }

            // initialize site settings
            Site::Init( $host );
            Logger::Debug( 'Site <b>%s</b> initialized', Site::$Name );

            if ( $autoDetectPage ) {
                PageManager::DetectPage();
            }
        }


        /**
         * Cache Sites.xml
         *
         * @param DOMDocument $doc  the sites.xml
         * @return DOMDocument
         */
        public static function CacheSitesXML( DOMDocument $doc ) {
            // Merge Site Settings
            $sitesList = $doc->getElementsByTagName( 'site' );
            foreach ( $sitesList as $node ) {
                /** @var $node DOMElement */
                if ( $node->hasAttribute( SiteManagerConstants::xmlExtends ) ) {
                    $xpath = new DOMXPath( $doc );

                    $exSiteName         = $node->getAttribute( SiteManagerConstants::xmlExtends );
                    $exSiteSettingsList = $xpath->evaluate( sprintf( SiteManagerConstants::siteSettingsQuery, $exSiteName ) );
                    $exSiteSettings     = $exSiteSettingsList->item( 0 );

                    if ( empty( $exSiteSettings ) ) {
                        Logger::Warning( 'Unknown site name %s for merging %s ', $exSiteName, $node->getAttribute( 'name' ) );
                    }

                    // Get Current Settings
                    $curSiteSettings = XmlHelper::GetChildNode( 'settings', $node );
                    if ( !empty( $curSiteSettings ) ) {
                        $mergedSiteSettings = XmlHelper::MergeNodes( $exSiteSettings, $curSiteSettings );
                        $node->replaceChild( $doc->importNode( $mergedSiteSettings, true ), $curSiteSettings );
                    } else {
                        $node->appendChild( $curSiteSettings );
                    }
                }
            }

            // Reformat hosts
            $hostLists = $doc->getElementsByTagName( 'host' );
            foreach ( $hostLists as $host ) {
                /** @var $host DOMElement */
                if ( !$host->hasAttribute( 'name' )
                    || ( trim( $host->getAttribute( 'name' ) ) == '' )
                ) {
                    Logger::Warning( 'Host with empty name!' );
                    continue;
                }


                // check for devel attr and apply it for all hosts and site
                if ( !$host->hasAttribute( SiteManagerConstants::xmlDevel )
                    && $host->parentNode->parentNode->hasAttribute( SiteManagerConstants::xmlDevel )
                ) {
                    $host->setAttribute( SiteManagerConstants::xmlDevel, $host->parentNode->parentNode->getAttribute( SiteManagerConstants::xmlDevel ) );
                }

                // host defaults
                foreach ( SiteManagerConstants::$hostDefaults as $key => $value ) {
                    $tag = XmlHelper::GetChildNode( $key, $host );
                    if ( empty( $tag ) ) {
                        $host->appendChild( $doc->createElement( $key, $value ) );
                    }
                }

                // override settings for hosts
                $localSettings = XmlHelper::GetChildNode( 'settings', $host );
                if ( !empty( $localSettings ) ) {
                    $curSiteSettings = XmlHelper::GetChildNode( 'settings', $localSettings->parentNode->parentNode->parentNode );

                    $mergedSiteSettings = XmlHelper::MergeNodes( $curSiteSettings, $localSettings );
                    $host->replaceChild( $doc->importNode( $mergedSiteSettings, true ), $localSettings );
                }
            }

            return $doc;
        }


        /**
         * Get Next Level Domain of HostName
         * @param string $host e.g. test.localhost.ru
         * @return string e.g. localhost.ru
         */
        public static function GetNextLevelDomain( $host ) {
            $result = '';
            $dotPos = strpos( $host, '.' );

            if ( $dotPos !== false ) {
                $result = substr( $host, $dotPos + 1 );
            }

            return $result;
        }

    }

?>