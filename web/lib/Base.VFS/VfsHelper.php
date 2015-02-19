<?php
    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /**
     * Vfs Helper for Render
     * @package    Base
     * @subpackage VFS
     * @author     Sergeyfast
     */
    class VfsHelper {

        /**
         * Form VFS File
         *
         * @param string   $controlName
         * @param string   $controlId
         * @param VfsFile  $file
         * @param string   $previewType none or image
         * @return string
         */
        public static function FormVfsFile( $controlName, $controlId, $file, $previewType = "none" ) {
            return self::renderVfsFile( $controlName, $controlId, $file, $previewType, false );
        }


        /**
         * Form Vfs Multi File
         * @param string  $controlName
         * @param string  $controlId
         * @param VfsFile $file
         * @param string  $previewType none or image
         * @return string
         */
        public static function FormVfsMultiFile( $controlName, $controlId, $file, $previewType = "none" ) {
            return self::renderVfsFile( $controlName, $controlId, $file, $previewType, true );
        }


        /**
         * @param string  $controlName
         * @param string  $controlId
         * @param VfsFile $file
         * @param string  $previewType none or image
         * @param bool    $selfDelete
         * @return string
         */
        private static function renderVfsFile( $controlName, $controlId, $file, $previewType = "none", $selfDelete = false ) {
            $value      = null;
            $class = $selfDelete ? 'vfsMultiFile' : 'vfsFile';
            $params = array( 'vfs:previewType' => $previewType, 'data-mode' => 'fileId' );

            if ( is_object( $file ) and $file instanceof VfsFile ) {
                $params['vfs:src']  = Site::GetWebPath( VfsUtility::RootDir . $file->path );
                $params['vfs:name'] = FormHelper::RenderToForm( $file->title );
                $value              = $file->fileId;
            }

            return FormHelper::FormHidden( $controlName, $value, $controlId, $class, $params );
        }


        /**
         * @param string $controlName
         * @param string $controlId
         * @param string $file
         * @param string $previewType
         * @return string
         */
        public static function FormVfsFilePath( $controlName, $controlId, $file, $previewType = 'none' ) {
            $value  = null;
            $class  = 'vfsFile';
            $params = array( 'vfs:previewType' => $previewType, 'data-mode' => 'path' );

            if ( $file && is_string( $file ) ) {
                $params['vfs:src']  = Site::GetWebPath( VfsUtility::RootDir . $file );
                $params['vfs:name'] = FormHelper::RenderToForm( $file );
                $value              = $file;
            }

            return FormHelper::FormHidden( $controlName, $value, $controlId, $class, $params );
        }


        /**
         * Form VFS Folder
         *
         * @param string     $controlName
         * @param string     $controlId
         * @param VfsFolder  $folder
         * @return string
         */
        public static function FormVfsFolder( $controlName, $controlId, $folder ) {
            $xhtml = '';
            if ( empty( $folder ) ) {
                $xhtml = sprintf( '<input type="hidden" class="vfsFolder" name="%s" id="%s" />'
                    , $controlName
                    , $controlId
                );
            } else if ( is_object( $folder ) ) {
                $xhtml = sprintf( '<input type="hidden" class="vfsFolder" name="%s" id="%s" value="%s" vfs:name="%s" />'
                    , $controlName
                    , $controlId
                    , $folder->folderId
                    , FormHelper::RenderToForm( $folder->title )
                );
            }

            return $xhtml;
        }
    }