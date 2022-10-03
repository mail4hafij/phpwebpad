<?php
/** -------------------------------------------------------------------------------------*
* Version: 4.0                                                                           *
* framework: https://github.com/mail4hafij/phpwebpad                                     *
* License: Free to use                                                                   *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

class WebContext {
  public static $URL_PREFIX = "https://";
  public static $LOCALHOST = "localhost";
  
  /** 
   * Returns the full URL 
   * i.e., https://www.hafij.com:3030/user/test?key=value
   * @return string
   */
  public static function getFullURL() {
    // For testing purpose
    if(!isset($_SERVER["SERVER_PORT"])) {
      return self::$LOCALHOST;
    }
    
    $pageURL = self::$URL_PREFIX;
    if($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }

  /**
   * Returns the domain URL 
   * i.e., https://www.hafij.com:3030
   * @return string
   */
  public static function getDomainURL() {
    // For testing purpose
    if(!isset($_SERVER["SERVER_PORT"])) {
      return self::$LOCALHOST;
    }
    
    $pageURL = self::$URL_PREFIX;
    if($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL;
  }
  
  /**
   * Returns the domain name 
   * Also removing the leading www if $exclude_www is true
   * i.e., hafij.com
   * @return string
   */
  public static function getDomainName($exclude_www = true) {
    // For testing purpose
    if(!isset($_SERVER["SERVER_NAME"])) {
      return self::$LOCALHOST;
    }
    
    if($exclude_www) {
      return ltrim($_SERVER["SERVER_NAME"], "www");
    }
    return $_SERVER["SERVER_NAME"];
  }
  
  public static function isLocalhost() {
    return strstr(self::getDomainName(), self::$LOCALHOST) !== false;
  }
    
  public static function getIP() {
    $ipaddress = '';
    if(isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
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
}
?>