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
  
  public static function validateName($name) {
    if(preg_match('/.*[A-Z].*[A-Z].*/', $name)) {
      // if contains more than one Uppercase latters
      return false;
    }
    return true;
  }

  public static function getRandomString() {
    return md5(uniqid(rand(), true));
  }
  
  public static function getRandomNumber() {
    return mt_rand(100000, 999999);
  }

  public static function clean($text, $clean_html = true, $keep_newline = true) {
    $text = trim($text);
    if($clean_html) {
      if($keep_newline) {
        return nl2br(strip_tags($text));
      } else {
        return strip_tags($text);
      }
    } else {
      return nl2br($text);
    }
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