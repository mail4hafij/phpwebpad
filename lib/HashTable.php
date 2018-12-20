<?php
class HashTable {
  private $data = array();
  
  public function __construct() {
    
  }

  public function add($index, $value) {
    $this->data[$index] = $value;
  }
  
  public function get($index) {
    return $this->data[$index];
  }

  public function remove($index) {
      unset($this->data[$index]);
  }
  
  public function size() {
    return sizeof($this->data);
  }
  
}

?>