<?php
    /**
 * File Info
 *
 * @package Eaze
 * @subpackage Core
 * @author sergeyfast
 */
    class FileInfo {

        /**
         * File Name
         *
         * @var string
         */
        private $fileName = '';


        /**
         * Constructor
         *
         * @param string $fileName  the file path
         */
        public function __construct( $fileName = null ) {
            if ( !is_null( $fileName ) && file_exists( $fileName ) ) {
                $this->fileName = realpath( $fileName );
            }
        }


        /**
         * Copy To
         *
         * @param string $targetFileName
         * @return bool
         */
        public function CopyTo( $targetFileName ) {
            if ( !file_exists( $this->fileName ) ) {
                return false;
            }

            $result = copy( $this->fileName, $targetFileName );

            return $result;
        }


        /**
         * Move To
         *
         * @param string $targetFileName
         * @return bool
         */
        public function MoveTo( $targetFileName ) {
            if ( !$this->CopyTo( $targetFileName ) ) {
                return false;
            }

            if ( !unlink( $this->fileName ) ) {
                return false;
            }

            $this->fileName = realpath( $targetFileName );

            return true;
        }


        /**
         * Get Extension
         *
         * @return string
         */
        public function GetExtension() {
            return DirectoryInfo::GetExtension( $this->fileName );
        }


        /**
         * Get Type
         *
         * @return string
         */
        public function GetType() {
            $type = false;
            //$type = mime_content_type( $this->fileName );
            if ( !$type ) {
                $type = "application/octet-stream";
            }

            return $type;
        }


        /**
         * Return File name
         *
         * @return string file name
         */
        public function GetName() {
            return basename( $this->fileName );
        }


        /**
         * Get Instance
         *
         * @static
         * @param string $fileName
         * @return FileInfo
         */
        public static function GetInstance( $fileName ) {
            if ( !file_exists( $fileName ) ) {
                return null;
            }

            $file = new FileInfo( $fileName );

            return $file;
        }


        /**
         * Get Full Name
         *
         * @return string
         */
        public function GetFullName() {
            return $this->fileName;
        }


        /**
         * Get Base Directory
         *
         * @return string full directory path
         */
        public function GetBaseDirectory() {
            return dirname( $this->fileName );
        }


        /**
         * Get File Size
         *
         * @return int
         */
        public function GetFileSize() {
            $size = filesize( $this->fileName );

            return $size;
        }


        /**
         * Delete filename
         * @return bool
         */
        public function Delete() {
            if ( !file_exists( $this->fileName ) ) {
                return false;
            }

            return unlink( $this->fileName );
        }
    }
?>