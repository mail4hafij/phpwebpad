<?php
/** -------------------------------------------------------------------------------------*
* Version: 2.0                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

class JSONResponse {
  private $var = array();

  /**
  * Set JSON variable.
  * @param string $name
  * @param string $value
  */
  public function setVar($name, $value) {
    $this->var[$name] = $value;
  }
  
  public function add($data) {
    $this->var[] = $data;
  }

  /**
  * Return JSON string.
  * @return string
  */
  public function getResponse() {
    return json_encode($this->var);
  }
}
?>