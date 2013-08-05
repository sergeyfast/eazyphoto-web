<?php
    /**
     * Image Helper
     *
     * @package Eaze
     * @subpackage Eaze.Helpers
     */
    class ImageHelper {

        /**
         * Resize JPEG  Image
         *
         * @param string       $original    the original file path
         * @param string       $thumbnail   the thumbnail path
         * @param integer      $max_width   thumb width
         * @param integer      $max_height  thumb height
         * @param integer      $quality     jpeg queality
         * @param boolean      $scale       scale thumb (true) or fixed size (false)
         * @return boolena operation result
         */
        public static function Resize( $original, $thumbnail, $max_width, $max_height, $quality, $scale = true ) {
            $imagetype = self::IsImage( $original );
            if ( false === $imagetype ) {
                return false;
            }

            list ($src_width, $src_height, $type, $w) = getimagesize($original);

            $srcImage = false;
            switch ($imagetype) {
                case "JPEG":
                    $srcImage = imagecreatefromjpeg($original);
                    break;
                case "PNG":
                    $srcImage = imagecreatefrompng($original);
                    break;
                case "GIF":
                    $srcImage = imagecreatefromgif($original);
                    break;
                case "BMP":
                    $srcImage = imagecreatefromwbmp($original);
                    break;
                default:
                    $srcImage = imagecreatefromgd2($original);
                    break;
            }


            if (!$srcImage ) {
                return false;
            }

            # image resizes to natural height and width
            if ($scale == true) {
                if( empty( $max_width ) || empty( $max_height ) || empty( $src_width ) || empty( $src_height ) ) {
                    return false;
                }

                $src_proportion     = $src_width / $src_height;
                $target_propotion   = $max_width / $max_height;

                if ( $src_height <= $max_height && $src_width <= $max_width  ) {
                    $thumb_width  = $src_width;
                    $thumb_height = $src_height;
                } else if( $src_proportion >= $target_propotion ) {
                    $thumb_width    = $max_width;
                    $thumb_height   = floor($src_height * ($max_width / $src_width));
                } else if( $src_proportion < $target_propotion ) {
                    $thumb_height   = $max_height;
                    $thumb_width    = floor($src_width * ($max_height / $src_height));
                } else {
                    $thumb_width = $max_height;
                    $thumb_height = $max_height;
                }

                if (!@$destImage = imagecreatetruecolor($thumb_width, $thumb_height)) {
                    return false;
                }

                if (!@imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $thumb_width, $thumb_height, $src_width, $src_height)) {
                    return false;
                }

            # image is fixed to supplied width and height and cropped
            } else if ($scale == false) {

                $ratio = $max_width / $max_height;

                 # thumbnail is not a square
                 if ($ratio != 1) {

                    $ratio_width = $src_width / $max_width;
                    $ratio_height = $src_height / $max_height;
                    if ($ratio_width > $ratio_height) {
                        $thumb_width = $src_width / $ratio_height;
                        $thumb_height = $max_height;
                    } else {
                        $thumb_width = $max_width;
                        $thumb_height = $src_height / $ratio_width;
                    }

                    $off_w = round( ( $thumb_width - $max_width ) / 2 );
                    $off_h = round( ( $thumb_height - $max_height ) / 2 );

                    if (!@$destImage = imagecreatetruecolor($max_width, $max_height)) {
                        return false;
                    }

                    if (!@imagecopyresampled($destImage, $srcImage, 0, 0, $off_w, $off_h, $thumb_width, $thumb_height, $src_width, $src_height)) {
                        return false;
                    }

                 # thumbnail is square
                 } else {
                    if ($src_width > $src_height) {
                        $off_w = ($src_width - $src_height) / 2;
                        $off_h = 0;
                        $src_width = $src_height;
                    } else if ($src_height > $src_width) {
                        $off_w = 0;
                        $off_h = ($src_height - $src_width) / 2;
                        $src_height = $src_width;
                    } else {
                        $off_w = 0;
                        $off_h = 0;
                    }

                    if (!@$destImage = imagecreatetruecolor($max_width, $max_height)) {
                        return false;
                    }

                    if (!@imagecopyresampled($destImage, $srcImage, 0, 0, $off_w, $off_h, $max_width, $max_height, $src_width, $src_height)) {
                        return false;
                    }
                 }
            }

            @imagedestroy($srcImage);

            if( function_exists( "imageantialias" ) ) {
                if (!@imageantialias($destImage, true)) {
                    return false;
                }
            }

            if ( !empty( $thumbnail ) ) {
                if (!@imagejpeg($destImage, $thumbnail, $quality)) {
                    return false;
                }

                @imagedestroy($destImage);
            } else {
                ob_start();
                imagejpeg($destImage, null, $quality);
                $result = ob_get_clean();

                imagedestroy($destImage);
                return $result;
            }

            return true;
        }

        /**
         * Crop JPEG image
         *
         * @static
         * @param string        $original       the original file path
         * @param string        $thumbnail      the thumbnail path
         * @param integer       $x              offset x
         * @param integer       $y              offset y
         * @param integer       $width          crop width
         * @param integer       $height         crop height
         * @param integer       $quality        jpeg quality
         * @return bool|string
         */
        public static function Crop( $original, $thumbnail, $x, $y, $width, $height, $quality ) {
            $imagetype = self::IsImage( $original );
            if ( false === $imagetype ) {
                return false;
            }

            list ($src_width, $src_height, $type, $w) = getimagesize($original);

            $srcImage = false;
            switch ($imagetype) {
                case "JPEG":
                    $srcImage = imagecreatefromjpeg($original);
                    break;
                case "PNG":
                    $srcImage = imagecreatefrompng($original);
                    break;
                case "GIF":
                    $srcImage = imagecreatefromgif($original);
                    break;
                case "BMP":
                    $srcImage = imagecreatefromwbmp($original);
                    break;
                default:
                    $srcImage = imagecreatefromgd2($original);
                    break;
            }


            if (!$srcImage ) {
                return false;
            }

            if (!@$destImage = imagecreatetruecolor($width, $height)) {
                return false;
            }

            if (!@imagecopyresampled($destImage, $srcImage, 0, 0, $x, $y, $width, $height, $width, $height)) {
                return false;
            }

            @imagedestroy($srcImage);

            if( function_exists( "imageantialias" ) ) {
                if (!@imageantialias($destImage, true)) {
                    return false;
                }
            }

            if ( !empty( $thumbnail ) ) {
                if (!@imagejpeg($destImage, $thumbnail, $quality)) {
                    return false;
                }

                @imagedestroy($destImage);
            } else {
                ob_start();
                imagejpeg($destImage, null, $quality);
                $result = ob_get_clean();

                imagedestroy($destImage);
                return $result;
            }

            return true;
        }

        /**
         * Is Image
         *
         * @param string $file
         */
        public static function IsImage( $file ) {
            $file_format = false;

            if ( !file_exists( $file ) ) {
                return $file_format;
            }

            //grab first 8 bytes, should be enough for most formats
            $image_data = fopen($file, "rb");
            $header_bytes = fread($image_data, 8);
            fclose ($image_data);

            //compare header to known signatures
            if (!strncmp ($header_bytes, "\xFF\xD8", 2))
                $file_format = "JPEG";
            else if (!strncmp ($header_bytes, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A", 8)) {
                $file_format = "PNG";
            } else if (!strncmp ($header_bytes, "FWS", 3) || !strncmp ($header_bytes, "CWS", 3 ) ) {
                $file_format = "SWF";
            } else if (!strncmp ($header_bytes, "BM", 2)) {
                $file_format = "BMP";
            }  else if (!strncmp ($header_bytes, "\x50\x4b\x03\x04", 4)) {
                $file_format = "ZIP";
            }  else if (!strncmp ($header_bytes, "GIF", 3)) {
                $file_format = "GIF";
            }  else if(!strncmp ($header_bytes, "\x49\x49\x2a\x00",4)) {
                $file_format = "TIF";
            } else if(!strncmp ($header_bytes, "\x4D\x4D\x00\x2a",4)) {
                $file_format = "TIF";
            }

            return $file_format;
        }


        /**
         * @static Returns dimensions of an image
         * @param string $filePath filesystem path to the image file
         * @return array Array with fields 'width' & 'height'
         */
        public static function GetImageSizes( $filePath ) {
            list ( $width, $height ) = getimagesize( $filePath );
            return array( 'width' => $width, 'height' => $height );
        }
    }
?>