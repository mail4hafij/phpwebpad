<?php

# ========================================================================#
#  Modified by: Mohammad Hafijur Rahman
#  Author:    Jarrod Oberto
#  Version:	 1.0
#  Date:      17-Jan-10
#  Purpose:   Resizes and saves image
#  Requires : Requires PHP5, GD library.
#  Usage Example:
#                     include("classes/resize_class.php");
#                     $resizeObj = new resize('images/cars/large/input.jpg');
#                     $resizeObj -> resizeImage(150, 100, 0);
#                     $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
#
#
# ========================================================================#

Class Resize {

  // *** Class variables
  private $image;
  private $imageResized;
  public $width;
  public $height;
  public $size;
  
  function __construct($fileName) {
    // *** Open up the file
    $this->image = $this->openImage($fileName);
    $this->imageResized = $this->openImage($fileName);
    
    // *** Get width and height
    $this->width = imagesx($this->image);
    $this->height = imagesy($this->image);
    $this->size = filesize($fileName);
  }

  ## --------------------------------------------------------

  private function openImage($file) {
    // *** Get extension
    $extension = strrchr($file, '.');

    switch (strtolower($extension)) {
      case '.jpg':
      case '.jpeg':
        $img = @imagecreatefromjpeg($file);
        break;
      case '.gif':
        $img = @imagecreatefromgif($file);
        break;
      case '.png':
        $img = @imagecreatefrompng($file);
        break;
      default:
        $img = false;
        break;
    }
    return $img;
  }

  ## --------------------------------------------------------

  public function resizeImage($newWidth, $newHeight, $option = "auto", 
    $watermarkPath = "", $padding = 0) {
    
    // we need to crop the image befor we resize the image if
    // the padding is not 0
    if($padding > 0) {
      
      // Save the current image 
      // and put it to crop
      // So we dont lose the orignial image before cropping.
      $crop = $this->image;
      $removedPaddingWidth = $this->width - ($padding * 2);
      $removedPaddingHeight = $this->height - ($padding * 2);
      
      // Create a canvas.
      $this->image = imagecreatetruecolor($removedPaddingWidth, $removedPaddingHeight);
      // Here we have lost the original image but we have saved that image in crop.
      // Now crop the image and put it back to our image variable.
      imagecopyresampled($this->image, $crop, 0, 0, $padding, $padding, 
      $removedPaddingWidth, $removedPaddingHeight, 
      $removedPaddingWidth, $removedPaddingHeight);
      
      // *** Now restting the new width and height
      $this->width = imagesx($this->image);
      $this->height = imagesy($this->image);
    }
    
    
    if(!empty($watermarkPath)) {
      // manage water mark
      $stamp = imagecreatefrompng($watermarkPath);

      // Set the margins for the stamp and get the height/width of the stamp image
      $marge_top = 10;
      $marge_left = 10;
      
      $marge_right = 10;
      $marge_bottom = 10;
      
      $sx = imagesx($stamp);
      $sy = imagesy($stamp);

      // Copy the stamp image onto our photo using the margin offsets and the photo 
      // width to calculate positioning of the stamp. 
      /*
      imagecopy($this->image, $stamp, imagesx($this->image) - $sx - $marge_right, 
        imagesy($this->image) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
      */
      imagecopy($this->image, $stamp, $marge_left, 
        $marge_top, 0, 0, imagesx($stamp), imagesy($stamp));
      
    }
    
    
    
    // *** Get optimal width and height - based on $option
    $optionArray = $this->getDimensions($newWidth, $newHeight, $option);
    $optimalWidth = $optionArray['optimalWidth'];
    $optimalHeight = $optionArray['optimalHeight'];

    
        
    // Resize the image to its optimal width and height.
    // *** Resample - create image canvas of x, y size
    $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
    imagecopyresampled($this->imageResized, $this->image, 
      0, 0, 0, 0, 
      $optimalWidth, $optimalHeight, $this->width, $this->height);

    
    
    // After resizing the image to its optimal width and height
    // we now crop the image if the option is 'crop'.
    // *** if option is 'crop', then crop too
    if ($option == 'crop') {
      $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
    }
  }

  ## --------------------------------------------------------

  private function getDimensions($newWidth, $newHeight, $option) {

    switch ($option) {
      case 'exact':
        $optimalWidth = $newWidth;
        $optimalHeight = $newHeight;
        break;
      case 'portrait':
        $optimalWidth = $this->getSizeByFixedHeight($newHeight);
        $optimalHeight = $newHeight;
        break;
      case 'landscape':
        $optimalWidth = $newWidth;
        $optimalHeight = $this->getSizeByFixedWidth($newWidth);
        break;
      case 'auto':
        $optionArray = $this->getSizeByAuto($newWidth, $newHeight);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];
        break;
      case 'crop':
        $optionArray = $this->getOptimalCrop($newWidth, $newHeight);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];
        break;
    }
    return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
  }

  ## --------------------------------------------------------

  private function getSizeByFixedHeight($newHeight) {
    $ratio = $this->width / $this->height;
    $newWidth = $newHeight * $ratio;
    return $newWidth;
  }

  private function getSizeByFixedWidth($newWidth) {
    $ratio = $this->height / $this->width;
    $newHeight = $newWidth * $ratio;
    return $newHeight;
  }

  private function getSizeByAuto($newWidth, $newHeight) {
    if ($this->height < $this->width) {
    // *** Image to be resized is wider (landscape)
      $optimalWidth = $newWidth;
      $optimalHeight = $this->getSizeByFixedWidth($newWidth);
    } elseif ($this->height > $this->width) {
    // *** Image to be resized is taller (portrait)
      $optimalWidth = $this->getSizeByFixedHeight($newHeight);
      $optimalHeight = $newHeight;
    } else {
    // *** Image to be resizerd is a square
      if ($newHeight < $newWidth) {
        $optimalWidth = $newWidth;
        $optimalHeight = $this->getSizeByFixedWidth($newWidth);
      } else if ($newHeight > $newWidth) {
        $optimalWidth = $this->getSizeByFixedHeight($newHeight);
        $optimalHeight = $newHeight;
      } else {
        // *** Sqaure being resized to a square
        $optimalWidth = $newWidth;
        $optimalHeight = $newHeight;
      }
    }

    return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
  }
  
  private function getOptimalCrop($newWidth, $newHeight) {

    $heightRatio = $this->height / $newHeight;
    $widthRatio = $this->width / $newWidth;

    if ($heightRatio < $widthRatio) {
      $optimalRatio = $heightRatio;
    } else {
      $optimalRatio = $widthRatio;
    }

    $optimalHeight = $this->height / $optimalRatio;
    $optimalWidth = $this->width / $optimalRatio;

    return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
  }

  ## --------------------------------------------------------

  private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {
    // *** Find center - this will be used for the crop
    $cropStartX = ( $optimalWidth / 2) - ( $newWidth / 2 );
    $cropStartY = ( $optimalHeight / 2) - ( $newHeight / 2 );

    $crop = $this->imageResized;
    //imagedestroy($this->imageResized);
    // *** Now crop from center to exact requested size
    $this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, 
      $newWidth, $newHeight, $newWidth, $newHeight);
  }

  ## --------------------------------------------------------

  public function saveImage($imgPath, $imageQuality = "100", $suffix = "") {
    // *** Get extension
    $extension = strrchr($imgPath, '.');
    $pathToSave = substr($imgPath, 0, (strlen($imgPath) - strlen($extension))) . $suffix . $extension;
    
    switch (strtolower($extension)) {
      case '.jpg':
      case '.jpeg':
        if (imagetypes() & IMG_JPG) {
          imagejpeg($this->imageResized, $pathToSave, $imageQuality);
        }
        break;

      case '.gif':
        if (imagetypes() & IMG_GIF) {
          imagegif($this->imageResized, $pathToSave);
        }
        break;

      case '.png':
        // *** Scale quality from 0-100 to 0-9
        $scaleQuality = round(($imageQuality / 100) * 9);

        // *** Invert quality setting as 0 is best, not 9
        $invertScaleQuality = 9 - $scaleQuality;

        if (imagetypes() & IMG_PNG) {
          imagepng($this->imageResized, $pathToSave, $invertScaleQuality);
        }
        break;

      // ... etc

      default:
        // *** No extension - No save.
        break;
    }
    
    // Lets destroy both images.
    // So when An image has to be resized 
    // The constructor needs to be called again.
    // I know calling the constructor several times
    // when we are resizing images could be a bad thing.
    // But we have optimzeproductimage where we 
    // load every image one by one from the product
    // directory. So, in that situation the memory
    // gets full when loading so many images and not 
    // destroying any original image from the memory.
    // That is why, I destroy both the original and 
    // resized image when we are finished with any 
    // resize. For the next resize we need to call the 
    // constructor again.
    imagedestroy($this->imageResized);
    imagedestroy($this->image);
  }

  ## --------------------------------------------------------
}

?>
