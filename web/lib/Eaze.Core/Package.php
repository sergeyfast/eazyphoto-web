<?php
    if ( !defined( 'CONFPATH_CACHE' ) ) {
        define( 'CONFPATH_CACHE', 'cache' );
    }

    /**
     * Package Loader
     *
     * Filename = Class name
     *
     * @package    Eaze
     * @subpackage Core
     * @author     sergeyfast
     */
    class Package {

        /**
         * Linux OS Constant
         */
        const OsLinux = 'Linux';

        /**
         * FreeBSD OS Constant
         */
        const OsFreeBSD = 'FreeBSD';

        /**
         * Windows OS Constant
         */
        const OsWindows = 'Windows';

        /**
         * File flag for WITH_PACKAGE_COMPILE check
         */
        const CompiledEaze = 'compiled.eaze';

        /**
         * Class Map
         */
        const ClassMap = 'classmap.json';

        /**
         * WITH_PACKAGE_COMPILE constant name
         */
        const WithPackageCompile = 'WITH_PACKAGE_COMPILE';

        /**
         * Loaded Packages
         *
         * @var array
         */
        public static $Packages = array();

        /**
         * Loaded Files
         *
         * @var array
         */
        public static $Files = array();

        /**
         * Structure of LIB Directory
         *
         * @var array
         */
        public static $LibStructure = array();

        /**
         * Loaded Classes
         * @var array system = before uri, uri = after uri
         */
        public static $LoadedClasses = array( 'system' => array(), 'uri' => array() );

        /**
         * Current Uri
         * @var array
         */
        public static $CurrentUri;

        /**
         * Force Disable Package Compile
         * @var bool
         */
        public static $DisablePackageCompile = false;

        /**
         * Handle for GetLock & ReleaseLock
         * @var resource
         */
        private static $lockHandle;

        /**
         * Autoload Event
         * @var bool
         */
        private static $autoloadEvent = false;


        /**
         * Current OS
         * @var string self::Os* Constants, or empty if not detected.
         */
        public static $CurrentOS;

        /**
         * Begin URI
         * @param $uri
         */
        public static function BeginUri( $uri ) {
            self::$CurrentUri = $uri;

            // include uri
            if ( Package::WithPackageCompile() ) {
                $file = self::GetCompiledFilename( 'uri', true );
                if ( is_file( $file ) ) {
                    /** @noinspection PhpIncludeInspection */
                    require_once $file;
                }
            }
        }


        /**
         * Load ClassMap from file
         * @return array system|classes|files
         */
        public static function GetClassMap() {
            Logger::Checkpoint();
            $filename = self::getClassMapFilename();
            $result   = array( 'system' => array(), 'classes' => array(), 'uri' => array(), 'md5Uri' => array() );

            if ( is_file( $filename ) ) {
                $result = json_decode( file_get_contents( $filename ), true );
            }

            Logger::Debug( 'Loaded classmap' );
            return $result;
        }


        /**
         * Save Class Map to file
         * @param $classmap
         * @return int
         */
        public static function SaveClassMap( $classmap ) {
            $filename = self::getClassMapFilename();
            $result   = file_put_contents( $filename, json_encode( $classmap ) );

            return $result;
        }


        /**
         * Get ClassMap filename with real path
         * @return string
         */
        private static function getClassMapFilename() {
            return sprintf( '%s/%s/%s', __ROOT__, CONFPATH_CACHE, self::ClassMap );
        }


        /**
         * Get Compiled Filename from Cache
         * @param string $type system | uri | mdtUri
         * @param bool   $withRealPath
         * @return string
         */
        public static function GetCompiledFilename( $type, $withRealPath = false ) {
            $fileType = $type;
            switch( $type ) {
                case 'uri':
                    $fileType = md5( self::$CurrentUri );
                    break;
                case 'system':
                    $fileType = 'system';
                    break;
            }

            $result = sprintf( 'package_%s.php', $fileType );
            if ( $withRealPath ) {
                $result = __ROOT__ . '/' . CONFPATH_CACHE . '/' . $result;
            }

            return $result;
        }


        /**
         * Include System Package File
         */
        public static function IncludeSystem() {
            $file = self::GetCompiledFilename( 'system', true );
            if ( is_file( $file ) ) {
                /** @noinspection PhpIncludeInspection */
                require_once $file;
            }
        }


        /**
         * Get Lock Depending on System (only in WITH_PACKAGE_COMPILE)
         * @return bool
         */
        public static function GetLock() {
            if ( !self::WithPackageCompile() ) {
                return false;
            }

            if ( self::$CurrentOS == self::OsWindows ) {
                if ( !is_file( self::GetPackageCompiledFlagFile() . '.lock' )
                    && touch( self::GetPackageCompiledFlagFile() . '.lock' ) )
                {
                    return true;
                }

                return false;
            } else { // Unix with flock LOCK_NB
                $handle = fopen( self::GetPackageCompiledFlagFile(), 'r+');
                if ( flock ( $handle, LOCK_EX | LOCK_NB ) ) {
                    self::$lockHandle = $handle;
                    return true;
                }

                return false;
            }
        }


        /**
         * Release Lock (only in WITH_PACKAGE_COMPILE)
         * @param bool $success if false - do not touch anything)
         * @return bool
         */
        public static function ReleaseLock( $success ) {
            if ( !$success ) {
                return false;
            }

            if ( !self::WithPackageCompile() ) {
                return false;
            }

            if ( self::$CurrentOS == self::OsWindows ) {
                return unlink( self::GetPackageCompiledFlagFile() . '.lock' );
            } else {
                return flock( self::$lockHandle, LOCK_UN );
            }
        }


        /**
         * Save Loaded classes to cache file
         */
        public static function Shutdown() {
            $lock = false;
            foreach( self::$LoadedClasses as $classes ) {
                if ( $classes ) {
                    $lock = true;
                    break;
                }
            }


            if ( $lock ) {
                $lock = self::GetLock();

                if ( self::$DisablePackageCompile ) {
                    Logger::Info( 'Package Compilation was disabled. Total Classes: %d', count( self::$LoadedClasses ) );
                } else if ( $lock ) { // Locked section of Package.php
                    $date     = date( 'c' );
                    $classmap = self::GetClassMap();
                    $uri      = md5( Package::$CurrentUri );

                    // initialize uri in classmap
                    if ( empty( $classmap['uri'][$uri] ) ) {
                        $classmap['uri'][$uri]    = array();
                        $classmap['md5Uri'][$uri] = Package::$CurrentUri;
                    }

                    // Main Loop
                    foreach ( self::$LoadedClasses as $type => $classes ) {
                        if ( $classes ) { // classes is system or uri
                            Logger::Checkpoint();

                            $buffer = '';
                            foreach ( $classes as $class => $filenames ) {
                                if ( !self::modifyClassMap( $classmap, $type, $class, $uri, $date ) ) {
                                    continue;
                                }

                                foreach( $filenames as $filepath ) {
                                    $buffer .= self::FormatPhpFileForCompile( file_get_contents( $filepath ) );
                                }
                            }

                            if ( $buffer ) {
                                file_put_contents( self::GetCompiledFilename( $type, true ), $buffer, FILE_APPEND | LOCK_EX );
                                Logger::Info( 'Writing %d classes to %s', count( $classes ), $type );
                            }
                        }
                    }

                    self::SaveClassMap( $classmap );
                } else {
                    Logger::Info( 'Failed to get lock on compiled packages' );
                }
            }

            self::ReleaseLock( $lock );
        }


        /**
         * @param array $classmap class map
         * @param string $type     system or uri
         * @param string $class    name of class
         * @param string $uri      md5 of uri
         * @param string $date     date
         * @return bool
         */
        private static function modifyClassMap( &$classmap, $type, $class, $uri, $date ) {
            if ( $type == 'system' ) {
                if ( array_key_exists( $class, $classmap['system'] ) ) {
                    return false;
                } else {
                    $classmap['system'][$class] = $date;
                    if ( array_key_exists( $class, $classmap['classes'] ) ) {
                        self::rebuildUriPackages( $classmap, $class );
                    }
                }
            } else if ( $type == 'uri' ) {
                 if ( array_key_exists( $class, $classmap['uri'][$uri] ) || array_key_exists( $class, $classmap['system'] ) ) {
                    return false;
                } else {
                    $classmap['uri'][$uri][$class]     = $date;
                    $classmap['classes'][$class][$uri] = $date;
                }
            }

            return true;
        }


        /**
         * Rebuild URI Packages (if class was loaded to system and exists in other urls)
         * @param array $classmap
         * @param string $class
         */
        private static function rebuildUriPackages( &$classmap, $class ) {
            $uris = array_keys( $classmap['classes'][$class] );
            foreach( $uris as $uri ) {
                unset( $classmap['uri'][$uri] );

                $filename = self::GetCompiledFilename( $uri, true );
                if ( is_file( $filename ) ) {
                    unlink( $filename );
                }
            }

            unset( $classmap['classes'][$class] );
        }


        /**
         * Format PHP File Content For Compilation
         * @param string $content
         * @return string
         */
        public static function FormatPhpFileForCompile( $content ) {
            $postfix = '';
            if ( !preg_match('/^\s*namespace\s*[{a-zA-Z0-9\\\\_]+/m', $content) ) {
                $content =  '<?php namespace {' . ltrim( $content, '<?phpPHP' );
                $postfix = ' } ';
            }

            $content = rtrim( trim( trim( $content ), PHP_EOL ), '?>' ) . $postfix .  '?>';
            return $content;
        }


        /**
         * Load Package
         * @deprecated
         * @param string $name
         * @return bool
         */
        public static function Load( $name ) {
            return true;
        }


        /**
         * Check PHP Filename and existence
         * @static
         * @param  string $file       filename
         * @param  string $packageDir directory path with trailing slash
         * @return bool
         */
        public static function CheckPHPFilename( $file, $packageDir ) {
            if ( $file == '.'
                || $file == '..'
                || strpos( $file, '.php' ) === false
                || !is_file( $packageDir . $file )
            ) {
                return false;
            }

            return true;
        }


        /**
         * Init Constants __LIB__ & __ROOT__
         *
         * @return void
         */
        public static function InitConstants() {
            if ( !defined( '__LIB__' ) ) {
                define( '__LIB__', realpath( dirname( __FILE__ ) . '/..' ) );
            }

            if ( !defined( '__ROOT__' ) ) {
                define( '__ROOT__', realpath( dirname( __FILE__ ) . '/../..' ) );
            }

            if ( !self::$CurrentOS ) {
                $os = strtoupper( substr( PHP_OS, 0, 3 ) );
                switch( $os ) {
                    case 'WIN':
                        self::$CurrentOS = self::OsWindows;
                        break;
                    case 'LIN':
                        self::$CurrentOS = self::OsLinux;
                        break;
                    case 'FRE':
                        self::$CurrentOS = self::OsFreeBSD;
                        break;
                }
            }
        }


        /**
         * Load Lib Directory Structure
         *
         * @return void
         */
        private static function initLibStructure() {
            $libDir = __LIB__ . '/';

            /** @var $libInfo DirectoryIterator */
            /** @var $packageInfo DirectoryIterator */
            /** @var $subPackageInfo DirectoryIterator */

            Logger::Checkpoint();
            $libIterator = new FilesystemIterator( $libDir, FilesystemIterator::SKIP_DOTS );
            foreach ( $libIterator as $libInfo ) {
                // Project.Package
                if ( $libInfo->isDir() ) {
                    $packageIterator = new FilesystemIterator( $libInfo->getPathname(), FilesystemIterator::SKIP_DOTS );
                    foreach ( $packageIterator as $packageInfo ) {
                        // Project.SubPackage
                        if ( $packageInfo->isDir() && $packageInfo->getFilename() != 'actions' ) {
                            $subpackageIterator = new FilesystemIterator( $packageInfo->getPathname(), FilesystemIterator::SKIP_DOTS );
                            foreach ( $subpackageIterator as $subPackageInfo ) {
                                if ( self::CheckPHPFilename( $subPackageInfo->getFilename(), $subPackageInfo->getPath() . '/' ) ) {
                                    Package::$LibStructure[strtolower($subPackageInfo->getFilename())][] = $subPackageInfo->getPathname();
                                }
                            }
                        } else {
                            if ( self::CheckPHPFilename( $packageInfo->getFilename(), $packageInfo->getPath() . '/' ) ) {
                                Package::$LibStructure[strtolower($packageInfo->getFilename())][] = $packageInfo->getPathname();
                            }
                        }
                    }
                }
            }

            Logger::Info( 'Lib Structure was initialized: %d classes', count( Package::$LibStructure ) );
        }


        /**
         * Load Classes
         * @param string   $args [optional]
         * @param string   $_    [optional]
         */
        public static function LoadClasses( $args = null, $_ = null ) {
            $classes = func_get_args();
            foreach ( $classes as $class ) {
                self::LoadClass( $class );
            }
        }


        /**
         * Load Class by Name
         *
         * @param string $className
         * @return bool
         */
        public static function LoadClass( $className ) {
            if ( class_exists( $className, false ) || interface_exists( $className, false ) ) {
                return true;
            }

            // Get Last Class of Namespace
            if ( strpos( $className, '\\' ) !== false ) {
                $classNames = explode( '\\', $className );
                $className  = end( $classNames );
            }

            if ( !self::$autoloadEvent ) {
                Logger::Debug( 'Autoload event on <b>%s</b>', $className );
                Package::initLibStructure();
                self::$autoloadEvent = true;
            }

            $fileName = strtolower($className) . '.php';
            if ( isset( Package::$LibStructure[$fileName] ) ) {
                /** @noinspection PhpIncludeInspection */
                foreach( Package::$LibStructure[$fileName] as $includeFile ) {
                    require_once( $includeFile );
                    Package::$LoadedClasses[Package::$CurrentUri ? 'uri' : 'system'][$className][] = $includeFile;
                }

                return true;
            }

            return false;
        }


        /**
         * Flush Compiled Cache (php files from Package)
         */
        public static function FlushCompiledCache() {
            $cacheDir = __ROOT__ . '/' . CONFPATH_CACHE . '/';
            $d = dir( $cacheDir );
            while ( false !== ( $file = $d->read() ) ) {
                if ( self::CheckPHPFilename( $file, $cacheDir ) && strpos( $file, 'package_' ) === 0 ) {
                    unlink( $cacheDir . $file );
                }

                if ( $file == self::ClassMap ) {
                    unlink( $cacheDir . $file );
                }
            }
            $d->close();
        }


        /**
         * Eaze Compile Packages Code
         * Remove packages from cache if not in WITH_PACKAGE_COMPILE or Flush Cache if WITH_PACKAGE_COMPILE && compiled.eaze do not exist
         */
        public static function DoCompiledCacheOperations() {
            $packageCompiledFlag = self::GetPackageCompiledFlagFile();
            if ( Package::WithPackageCompile() ) {
                if ( !file_exists( $packageCompiledFlag ) ) {
                    Package::FlushCompiledCache();
                    touch( $packageCompiledFlag );
                }
            } else if ( defined( Package::WithPackageCompile ) && file_exists( $packageCompiledFlag ) ) {
                unlink( $packageCompiledFlag );
                Package::FlushCompiledCache();
                Logger::Info( 'Removing old package cache' );
            }
        }


        /**
         * Get Compiled Lock Filename
         * @return string
         */
        public static function GetPackageCompiledFlagFile() {
            return sprintf( '%s/%s/%s', __ROOT__, CONFPATH_CACHE, Package::CompiledEaze );
        }



        /**
         * Get With Package Compiled Constant value
         * @return bool
         */
        public static function WithPackageCompile() {
            if ( !self::$DisablePackageCompile && defined( Package::WithPackageCompile ) ) {
                if ( WITH_PACKAGE_COMPILE ) {
                    return true;
                }
            }

            return false;
        }
    }

    Package::InitConstants();
    Package::DoCompiledCacheOperations();

    spl_autoload_register( 'Package::LoadClass' );
    if ( Package::WithPackageCompile() ) {
        register_shutdown_function( 'Package::Shutdown' );
        Package::IncludeSystem();
    }
?>