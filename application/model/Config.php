<?php
class Config {
  /**
  * Default config START
  */
  public static function validateEmail($email) {
    if(preg_match('/.*\@.*\..*/i', $email) && !preg_match('/\s/',$email)) {
      return true;
    }
    return false;
  }
  
  public static function getRandomString() {
    return md5(uniqid(rand(), true));
  }
  
  public static function getRandomNumber() {
    return mt_rand(100000, 999999);
  }

  public static function getRandomFileName() {
    return md5(uniqid(rand(), true));
  } 
  
  public static function replaceWhiteSpace($str, $replace) {
    return preg_replace('/\s+/', $replace, $str);
  }

  /**
  * Default config END
  */
}
?>