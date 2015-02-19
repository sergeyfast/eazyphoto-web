<?php

    use Eaze\Helpers\ImageHelper;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\IFactory;
    use Eaze\Model\ObjectInfo;
    use Eaze\Site\Site;

    /**
     * Object Image Utility
     * @package    EazyPhoto
     * @subpackage Common
     * @author     Sergeyfast
     */
    class ObjectImageUtility {

        /**
         * Default Small Image Size
         * @var int[]
         */
        public static $DefaultImageSize = [ 100, 100 ];

        /**
         * Default Folder Id
         * @var int
         */
        public static $DefaultFolderId = 1;


        /**
         * Initialize Mappings
         * @static
         * @param IFactory $factory
         */
        public static function InitializeMappings( IFactory $factory ) {
            $factoryClass = get_class( $factory );
            $mapping      = BaseFactory::GetMapping( $factoryClass );

            ObjectImageFactory::$mapping['fields']['objectClass']['default'] = $mapping['class'];
            unset( $mapping['fields']['images'] );
            $mapping['lists']['images'] = [
                'name'       => 'objectId',
                'foreignKey' => 'ObjectImage',
            ];

            $rc = new ReflectionClass( $factoryClass );
            $rc->setStaticPropertyValue( 'mapping', $mapping );
        }


        /**
         * Prepare Album Data for Template
         * @static
         * @param $object
         * @return array
         */
        public static function PrepareImagesData( $object ) {
            $result = [ ];
            if ( $object->images === null ) {
                $object->images = [ ];
            }

            $index = 0;
            foreach ( $object->images as $image ) {
                $imageItem = [
                    'id'      => $image->objectImageId,
                    'imgName' => $image->title,
                    'num'     => $index++,
                ];

                if ( !empty( $image->bigImageId ) ) {
                    $imageItem['bigImage'] = [
                        'id'   => $image->bigImage->fileId,
                        'name' => $image->bigImage->title,
                        'src'  => $image->bigImage->path,
                    ];
                }

                if ( !empty( $image->smallImageId ) ) {
                    $imageItem['smallImage'] = [
                        'id'   => $image->smallImage->fileId,
                        'name' => $image->smallImage->title,
                        'src'  => $image->smallImage->path,
                    ];
                }

                $result[] = $imageItem;
            }

            return $result;
        }


        /**
         * Save Images to Database
         * @static
         * @param StaticPage|mixed $object
         * @param StaticPage|mixed $originalObject
         * @return bool
         */
        public static function SaveImages( $object, $originalObject = null ) {
            $imageOrderNumber = 1;

            $oi = ObjectInfo::Get( $object );
            if ( !$oi ) {
                return false;
            }

            foreach ( $object->images as $image ) {
                $image->objectId    = $oi->Id;
                $image->objectClass = $oi->Class;
                $image->statusId    = StatusUtility::Enabled;
                $image->orderNumber = $imageOrderNumber++;
            }

            return ObjectImageFactory::SaveArray( $object->images, !empty( $originalObject ) && !empty( $originalObject->images ) ? $originalObject->images : [ ] );
        }


        /**
         * Reformat Errors Array
         * @static
         * @param $errors
         * @param $unusedFields
         * @return array
         */
        private static function filterUnusedErrorFields( $errors, $unusedFields ) {
            $result = [ ];
            if ( !empty( $errors['fields'] ) ) {
                foreach ( $unusedFields as $unused ) {
                    if ( !empty( $errors['fields'][$unused] ) ) {
                        unset( $errors['fields'][$unused] );
                    }
                }

                $result = !empty( $errors['fields'] ) ? $errors['fields'] : [ ];
            }

            return $result;
        }


        /**
         * Validate Images
         * @static
         * @param ObjectImage[] $images
         * @return array
         */
        public static function ValidateImages( $images ) {
            $result = [ ];
            if ( empty( $images ) ) {
                return $result;
            }

            $unusedImageFields = [ 'objectId', 'objectClass', 'orderNumber', 'statusId' ];

            foreach ( $images as $imageIndex => $image ) {
                $errors = ObjectImageFactory::Validate( $image );
                $errors = self::filterUnusedErrorFields( $errors, $unusedImageFields );

                if ( !empty( $errors ) ) {
                    $result[$imageIndex] = $errors;
                }
            }

            return $result;
        }


        /**
         * Create Thumbnail
         * @param VfsFile $bigImage
         * @param int     $width
         * @param int     $height
         * @return VfsFile
         */
        public static function CreateThumbnail( VfsFile $bigImage, $width = null, $height = null ) {
            $width  = $width ? $width : self::$DefaultImageSize[0];
            $height = $height ? $height : self::$DefaultImageSize[1];
            $path   = Site::GetRealPath( 'vfs://' . $bigImage->path );
            $thumb  = Site::GetRealPath( 'temp://s_' . $bigImage->fileId . '.jpg' );
            $result = ImageHelper::Resize( $path, $thumb, $width, $height, 100, false );
            $result = $result ? VfsUtility::CreateFile( $bigImage->folder->folderId, 's_' . $bigImage->title, $thumb, 'image/jpg' ) : false;
            if ( $result ) {
                return $result;
            }

            return false;
        }

    }