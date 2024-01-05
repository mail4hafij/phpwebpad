<?php
session_start();

class Captcha {
  private $image;
  
  public function __construct($width='260', $height='40', $length='6') {
    $font = dirname(__FILE__).'/ERECTL__.ttf';
    
    $code = $this->generateCode($length);
    /* font size will be 75% of the image height */
    $font_size = $height * 0.75;
    $this->image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');

    /* set the colours */
    $background_color = imagecolorallocate($this->image, 98, 153, 197);
    $text_color = imagecolorallocate($this->image, 255, 255, 255);
    
    /*
    $noise_color = imagecolorallocate($this->image, 59, 255, 152);
    
    // generate random dots in background
    for( $i=0; $i<($width*$height)/3; $i++ ) {
      imagefilledellipse($this->image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
    }
    
    // generate random lines in background
    for( $i=0; $i<($width*$height)/150; $i++ ) {
    imageline($this->image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
    */
    
    /* create textbox and add text */
		$textbox = @imagettfbbox($font_size, 0, $font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		@imagettftext($this->image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Error in imagettftext function');
    
    // Setting the code in the session.
    $_SESSION['security_code'] = $code;
	}
  
  private function generateCode($length) {
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
  
  public function outputImage() {
    /* output captcha image to browser */
    header('Content-Type: image/jpeg');
    imagejpeg($this->image);
    imagedestroy($this->image);
  }
}


$width = isset($_GET['width']) ? $_GET['width'] : '200';
$height = isset($_GET['height']) ? $_GET['height'] : '40';
$length = isset($_GET['length']) && $_GET['length'] > 1 ? $_GET['length'] : '6';
$captcha = new Captcha($width, $height, $length);

// output the image
$captcha->outputImage();
?>