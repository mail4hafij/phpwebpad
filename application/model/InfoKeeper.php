<?php
class InfoKeeper {
  public static $_STATUS = "CURRENT_STATUS";
  public static $_ID_LIST = "ID_LIST";
  public static $_SETTINGS = "SETTINGS";
  
  
  public static function setCurrentStatus($status) {
    $_SESSION[InfoKeeper::$_STATUS] = $status;
  }

  public static function getCurrentStatus() {
    if(!isset($_SESSION[InfoKeeper::$_STATUS])) return '';
    $msg = $_SESSION[InfoKeeper::$_STATUS];
    InfoKeeper::setCurrentStatus(null);
    return $msg;
  }
  
  public static function getSecureId($id) {
    if(!isset($_SESSION[InfoKeeper::$_ID_LIST])) {
      $id_list[$id] = sha1($id);
    } else {
      $id_list = $_SESSION[InfoKeeper::$_ID_LIST];
      if(!isset($id_list[$id])) {
        $id_list[$id] = sha1($id);
      } else {
        return $id_list[$id];
      }
    }
    $_SESSION[InfoKeeper::$_ID_LIST] = $id_list;
    return $id_list[$id];
  }
  
  public static function getUnSecureId($secure_id) {
    if(isset($_SESSION[InfoKeeper::$_ID_LIST])) {
      $id_list = $_SESSION[InfoKeeper::$_ID_LIST];
      $id = array_search($secure_id, $id_list);
      if($id !== FALSE) {
        return $id;
      }
    }
    throw new Exception("Can not find unsecure id");
  }
  
  public static function getSettings() {
    if(!isset($_SESSION[self::$_SETTINGS])) {
      $_SESSION[self::$_SETTINGS] = new Settings();
    } 
    return $_SESSION[self::$_SETTINGS];
  }
  
  public static function setAntiForgery($key, $val = null) {
    $_SESSION[$key] = $val;
  }

  public static function getAntiForgery($key) {
    if(!isset($_SESSION[$key])) return null;
    $msg = $_SESSION[$key];
    return $msg;
  }
}

?>
