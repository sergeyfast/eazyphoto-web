<?php
    class GetCaptcha {
 
        /**
         * Execute GetCaptcha
         */
        public function Execute() {
        	/**
        	 * Generate Random Key
        	 */
			$key     = rand( 10000, 99999 );
			
			/**
			 * Create Captcha
			 */
			$captcha = imagecreatefromjpeg( Site::GetRealPath( "images://fe/captcha.jpg" ) ); 
			
			/**
			 * Line color
			 */
			$lineColor = imagecolorallocate( $captcha, 233, 239, 239);

			/**
			 * Text Color
			 */
			$textColor = imagecolorallocate( $captcha, 0, 0, 0);
			
			/**
			 * Draw lines
			 */
			imageline( $captcha, 1, 1, 40, 40, $lineColor);
			imageline( $captcha, 1, 100, 60, 0, $lineColor);
			
			/**
			 * Draw string(key)
			 */
			imagestring($captcha, 5, 27, 8, $key, $textColor); 
			
			/**
			 * Set key to session
			 */
			Session::setString( "captcha", $key );
			
			/**
			 * Return
			 */
			header("Content-type: image/jpeg"); 
			imagejpeg($captcha);
        }
    }
?>