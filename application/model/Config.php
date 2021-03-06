<?php

class Config {

  public static function getDateTime() {
    return gmdate('Y-m-d H:i:s', time());
  }

  public static function validateEmail($email) {
    if (preg_match('/.*\@.*\..*/i', $email))
      return true;
    return false;
  }

  public static function getCurrentURL() {
    $pageURL = "http://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }

  public static function getRandomString() {
    return md5(uniqid(rand(), true));
  }
  
  public static function getRandomFileName() {
    return md5(uniqid(rand(), true));
  } 

  public static function clean($text, $keep_html = false) {
    $text = trim($text);
    if($keep_html != true) {
      $text = strip_tags($text);
    }
    
    return $text;
  }

  public static function getUserBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = '';
    if (preg_match('/MSIE/i', $u_agent)) {
      $ub = "ie";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
      $ub = "firefox";
    } elseif (preg_match('/Safari/i', $u_agent)) {
      $ub = "safari";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
      $ub = "chrome";
    } elseif (preg_match('/Flock/i', $u_agent)) {
      $ub = "flock";
    } elseif (preg_match('/Opera/i', $u_agent)) {
      $ub = "opera";
    }

    return $ub;
  }
  
  public static function getCurrentIP() {
    return $_SERVER['REMOTE_ADDR'];
  }

}

?>
