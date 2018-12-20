<?php
/** -------------------------------------------------------------------------------------*
* Version: 1.2                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

/**
* Any model class that needs to use remote models must extend this ServiceModel class.
*/
abstract class ServiceModel extends Model{
  
  /**
   * Had to make this method static for the __callStatic method to work.
   * @return string
   */
  public static function getServiceUrl(){
    return "http://localhost/service";
  }
  
  public function __construct($db_or_info = null) {
    if($db_or_info == null) {
      parent::__construct();
    } else {
      if($db_or_info instanceof Database) {
        parent::__construct($db_or_info);
      } else {
        parent::__construct();
        $this->setInfo((array)$db_or_info);
      }
    }
  }

  // User/loadAll?arg1=val1&arg2=val2
  // User/loadAll?arg1=Company(id)&arg=val2
  // User(id)/edit?arg1=val1&arg2=val2
  // User(id)/edit?arg1=Company(id)&arg2=val2
  // User(id)
  
  private static function handleArgument($arguments) {
    $arg_str = "";
    $i = 1;
    foreach($arguments as $arg) {
      if(is_object($arg)) {
        $primary_key_name = strtolower(get_class($arg))."_id";
        $arg_str = $arg_str."arg$i=".get_class($arg)."(".eval("return \$arg->\$primary_key_name;").")&";
      } elseif($arg != null) {
        $arg_str = $arg_str."arg$i=".urlencode($arg)."&";
      }
      $i++;
    }
    $arg_str = rtrim($arg_str, "&");
    return $arg_str;
  }
  
  private static function handleData($data) {
    if(isset($data->error)) {
      throw new Exception($data->error);
    }
    
    $class_name = get_called_class();
    if((is_string($data) || is_int($data)) && $data != null) {
      return $data;
    } elseif($data != null){
      $is_multi = true;
      $obj = array();
      foreach($data as $info) {
        if(is_object($info)) {
          $obj[] = new $class_name($info);
        } else {
          $is_multi = false;
          break;
        }
      }
      
      return $is_multi ? $obj : new $class_name($data);
    }
  }
  
  public function __call($name, $arguments) {
    $arg_str = self::handleArgument($arguments);
    $url = rtrim(self::getServiceUrl(), "/");
    
    $class_name = get_called_class();
    $obj = new $class_name();
    if(method_exists($obj, $name)) {
      // Method exists in the current class.
      // This is how we call the extenstion method.
      return $obj->$name($arguments);
    } else {
      $primary_key_name = strtolower($class_name)."_id";
      $primary_key_value = $this->$primary_key_name;
      // echo("$url/$class_name($primary_key_value)/$name?$arg_str");
      $json = file_get_contents("$url/$class_name($primary_key_value)/$name?$arg_str"); 
      $data = json_decode($json);

      return self::handleData($data);
    }
  }
  
  public static function __callStatic($name, $arguments) {
    $arg_str = self::handleArgument($arguments);
    $url = rtrim(self::getServiceUrl(), "/");
    
    $class_name = get_called_class();
    $obj = new $class_name();
    if(method_exists($obj, $name)) {
      // Method exists in the current class.
      // This is how we call the extenstion method.
      return $obj::$name($arguments);
    } else {
      // echo("$url/$class_name/$name?$arg_str");
      $json = file_get_contents("$url/$class_name/$name?$arg_str");
      $data = json_decode($json);

      return self::handleData($data);
    }
  }
}
?>
