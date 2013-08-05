<?php
    /**
     * DirectoryInfo
     *
     * @package Eaze
     * @subpackage Core
     * @author sergeyfast
     */
    class DirectoryInfo {

        /**
         * Directory Path
         *
         * @var string
         */
        private $directoryPath = '';

        /**
         * Root Dir
         *
         * @var string
         */
        private $rootDirectory = '';


        /**
         * Create Instance
         *
         * @param string $directoryPath
         * @return DirectoryInfo
         */
        public function __construct( $directoryPath ) {
            if ( !is_null( $directoryPath ) && is_dir( $directoryPath ) ) {
                $this->directoryPath = realpath( $directoryPath );
            }
        }


        /**
         * Get Extension
         *
         * @static
         * @param string $fileName
         * @return string
         */
        public static function GetExtension( $fileName ) {
            $pointPosition = mb_strrpos( $fileName, "." );

            if ( false === $pointPosition ) {
                return null;
            }

            $extension = mb_strtolower( mb_substr( $fileName, $pointPosition + 1 ) );

            return $extension;
        }


        /**
         * Get Instance
         *
         * @param string $rootDirectory
         * @param string $relativeDirectory
         * @return DirectoryInfo
         */
        public static function GetInstance( $rootDirectory, $relativeDirectory = null ) {
            if ( !is_dir( $rootDirectory ) ) {
                return null;
            }


            if ( !empty( $relativeDirectory ) ) {
                $rootDirectory .= str_replace( '..', '', $relativeDirectory );

                return DirectoryInfo::GetInstance( $rootDirectory );
            } else {
                $directory = new DirectoryInfo( $rootDirectory );
            }

            return $directory;
        }


        /**
         * Create Sub Dir
         *
         * @param string $newDirectory
         * @return bool
         */
        public function CreateSubDirectory( $newDirectory ) {
            if ( ( is_null( $this->directoryPath ) )
                 || ( empty( $newDirectory ) )
            ) {
                return ( null );
            }

            $path = sprintf( '%s/%s', $this->directoryPath, $newDirectory );

            if ( file_exists( $path ) || is_dir( $path ) ) {
                return false;
            }

            $result = mkdir( $path );

            return $result;
        }


        /**
         * Get Page Count
         *
         * @param integer $pageSize
         * @return float
         */
        public function Count( $pageSize = 10 ) {
            $count = $this->getCount();

            if ( !empty( $count ) ) {
                $count = 0;
            }

            return $count / $pageSize;
        }


        /**
         * Get Count
         *
         * @return integer
         */
        private function getCount() {
            if ( is_null( $this->directoryPath ) ) {
                return null;
            }

            $i = 0;

            if ( $handle = opendir( $this->directoryPath ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( ( $file != "." )
                         && ( $file != ".." )
                    ) {
                        $i++;
                    }
                }

                closedir( $handle );
            }

            return $i;
        }


        /**
         * Get Files And Folders
         * @param int $page
         * @param int $pageSize
         *
         * @return array
         */
        public function GetAll( $page = 0, $pageSize = 10 ) {
            if ( is_null( $this->directoryPath ) ) {
                return null;
            }

            $list = array();
            $i = 0;

            if ( $handle = opendir( $this->directoryPath ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( ( $file != "." )
                         && ( $file != ".." )
                    ) {
                        /// Check Pages
                        $start = $page * $pageSize;
                        $end = $start + $pageSize;

                        if ( ( !empty( $start ) ) && ( $i < $start ) ) {
                            $i++;
                            continue;
                        }
                        if ( ( !empty( $end ) ) && ( $i >= $end ) ) {
                            break;
                        }

                        /// Get Files       
                        $file = $this->directoryPath . "/" . $file;

                        $list[$i] = array(
                            'path'       => $file
                            , 'fullName' => basename( $file )
                        );

                        // Check File Options
                        if ( is_file( $file ) ) {
                            $list[$i]['isDir']     = false;
                            $list[$i]['name']      = basename( $file, "." . $this->getExtension( $file ) );
                            $list[$i]['extension'] = $this->getExtension( $file );
                            $list[$i]['size']      = filesize( $file );
                        } else {
                            $list[$i]['isDir']     = true;
                        }

                        $i++;
                    }
                }

                closedir( $handle );
            }

            return $list;
        }


        /**
         * Returns file list with specified mask
         *
         * @param string $mask
         * @return array
         */
        public function GetFiles( $mask = null ) {
            if ( is_null( $this->directoryPath ) ) {
                return null;
            }

            $files = array();

            if ( $handle = opendir( $this->directoryPath ) ) {
                while ( false !== ( $file = readdir( $handle ) ) ) {
                    if ( ( $file != "." )
                         && ( $file != ".." )
                         && ( is_file( $this->directoryPath . "/" . $file ) )
                    ) {
                        if ( ( !empty( $mask ) )
                             && ( strpos( $file, $mask ) === false )
                        ) {
                            continue;
                        }

                        $files[] = array(
                            'filename'    => $file
                            , 'path'      => realpath( $this->directoryPath . "/" . $file )
                            , 'name'      => basename( $file, "." . $this->getExtension( $file ) )
                            , 'extension' => $this->getExtension( $file )
                        );
                    }
                }

                closedir( $handle );
            }

            return $files;
        }


        /**
         * Get Directory Path
         *
         * @return string
         */
        public function GetDirectoryPath() {
            return $this->directoryPath;
        }


        /**
         * Reformat Path
         *
         * @param string $path
         * @return string
         */
        public static function FormatPath( $path ) {
            return str_replace( '\\', '/', $path );
        }


        /**
         * Get Relative Path
         *
         * @param string $dir
         * @return string
         */
        public function GetRelativePath( $dir = null ) {
            $rootDir = self::FormatPath( $this->rootDirectory );
            $newDir = self::FormatPath( $this->directoryPath . '/' . $dir );

            $result = str_replace( $rootDir, '/', $newDir );

            return $result;
        }


        /**
         * Get Parent Path
         *
         * @return string
         */
        public function GetParentPath() {
            $rootDir = self::FormatPath( $this->rootDirectory );
            $newDir = self::FormatPath( $this->directoryPath . "/" );

            $result = str_replace( $rootDir, '/', $newDir );

            $result = self::FormatPath( dirname( $result ) );
            return $result;
        }
    }

?>