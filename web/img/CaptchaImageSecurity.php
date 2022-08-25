<?php
session_start();

class CaptchaImageSecurity {
  var $font = 'ERECTL__.ttf';
	public function generateCode($length) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '123456789abcdefghijklmnopqrstuvwxyz';
		$code = '';
		$i = 0;
		while ($i < $length) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}
  
	public function __construct($width='260', $height='40', $length='6') {
		$code = $this->generateCode($length);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
    
		/* set the colours */
		$background_color = imagecolorallocate($image, 98, 153, 197);
		$text_color = imagecolorallocate($image, 255, 255, 255);
    
    /*
		$noise_color = imagecolorallocate($image, 59, 255, 152);
    
    // generate random dots in background
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
    
		// generate random lines in background
    for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
    */
    
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
    
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		$_SESSION['security_code'] = $code;
	}
}

$width = isset($_GET['width']) ? $_GET['width'] : '260';
$height = isset($_GET['height']) ? $_GET['height'] : '40';
$length = isset($_GET['length']) && $_GET['length'] > 1 ? $_GET['length'] : '6';
$captcha = new CaptchaImageSecurity($width, $height, $length);
?>