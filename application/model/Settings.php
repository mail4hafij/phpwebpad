<?php
class Settings {
  public $data;
  public function __construct() {
    $json = file_get_contents('settings.json');
    // When true, JSON objects will be returned as associative arrays.
    // when false, JSON objects will be returned as objects. 
    // When null, JSON objects will be returned as associative arrays or objects depending 
    // on whether JSON_OBJECT_AS_ARRAY is set in the flags.
    $this->data = json_decode($json, false);
  }
  
  public function __get($name) {
    return $this->data->{$name};
  }
}
?>
