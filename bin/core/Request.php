<?php
/** -------------------------------------------------------------------------------------*
* Version: 2.0                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/


/**
* This class convert the server url to valid request.
* It reads the url then clean it up and tries to 
* split the url into three different portions.
* e.g. /user/settings/value1/value2?edit=true
* controller name: user.
* action name: settings.
* parameters: array(0 => value1, 1 => value2).
* Any $_GET or $_POST will be ignored so that it can
* be used in the controller itself.
*/
class Request {
  private $URI = null;
  private $controller_name = DEFAULT_CONTROLLER_NAME;
  private $action_name = DEFAULT_ACTION_NAME;
  private $parameters = array();

  /**
  * Constructor will clean up the request url.
  * It splits the url into three parts (controller, action, parameters).
  * @Exception when the request is empty.
  */
  public function  __construct($URI) {
    if(empty($URI)) {
      throw new Exception('FATAL: Request is empty.');
    }
    $this->URI = $URI;
    
    if(strstr($URI, "?")) {
      $URI = substr($URI, 0, strpos($URI, "?"));
    }
    $URI = trim($URI, "/");
    
    $params = explode("/", $URI);
    
    // Controller name
    if(isset($params[0]) && $params[0] != "") {
      $this->controller_name = $params[0];
    }

    // Action name
    if(isset($params[1]) && $params[1] != "") {
      $this->action_name = $params[1];
    }

    // Parameters
    $parameters = array();
    for($i = 2; $i < count($params) ; $i++) {
      $parameters[] = $params[$i];
    }
    $this->parameters = $parameters;
  }

  /**
  * Let only the router class to call this method.
  * @param string $controller_name.
  * @param string $action_name.
  * @param array $parameters.
  */
  public function modifyAsRouter($controller_name, $action_name, $parameters) {
    $this->controller_name = $controller_name;
    $this->action_name = $action_name;
    $this->parameters = $parameters;
  }

  public function getControllerName() { return $this->controller_name; }
  public function getActionName() { return $this->action_name; }
  public function getParameters() { return $this->parameters; }
  public function getURI() { return $this->URI; }
}
?>