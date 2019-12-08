<?php
/** -------------------------------------------------------------------------------------*
* Version: 2.0                                                                           *
* License: Free to use                                                                   * 
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

class Router {
  private static $special_route = array();
  private static $request = null;

  /**
  * First we try to find the controller file
  * if not then we check the special route.
  * Render the controller, action, and view file.
  * @Exception if the request can not be served.
  * @return void.
  */
  public static function render(Request $request) {
    self::$request = $request;
    if(!file_exists(self::getControllerFile())) {
      if(!self::isSpecialRouteThenModifyRequest()) {
        throw new Exception("FATAL: Request can not be served.");
      }
    }
    
    $root_controller_file = self::getRootControllerFile();
    $controller_file = self::getControllerFile();
    include_once($root_controller_file);
    include_once($controller_file);
    $dyn_class_obj = self::getController();
    $dyn_class_obj->beforRender($request->getActionName(), $request->getControllerName());
    $dyn_class_obj->renderAction($request->getActionName(), $request->getParameters());
    $dyn_class_obj->afterRender($request->getActionName(), $request->getControllerName());
    $dyn_class_obj->renderView($request->getControllerName());
  }

  /**
  * Controller class object that extends root controller.
  * e.g. ApplicationController extends Controller.
  * This method will return ApplicationController object in this example.
  * @return Contoller object.
  */
  private static function getController() {
    $class_name = ucfirst(self::$request->getControllerName()).ucfirst(ROOT_CONTROLLER_NAME);
    $dyn_class_obj = new $class_name();
    return $dyn_class_obj;
  }

  /**
  * Return the file path of the controller.
  * Here we don't check if the file exist or not.
  * @return string.
  */
  private static function getControllerFile() {
    return CONTROLLER.ucfirst(self::$request->getControllerName()).ucfirst(ROOT_CONTROLLER_NAME).".php";
  }

  /**
  * Return the file path of the root controller.
  * @exception if the file does not exist.
  * @return string.
  */
  private static function getRootControllerFile() {
    $path = CORE.ucfirst(ROOT_CONTROLLER_NAME).".php";
    if(!file_exists($path)) {
      throw new Exception('Root controller file can not be found.');
    }
    return $path;
  }

  /**
  * Check if the request is the special route.
  */
  private static function isSpecialRouteThenModifyRequest() {
    $flag = false;
    
    foreach(self::$special_route as $r) {
      if(preg_match($r[0], self::$request->getURI())) {
        
        // Basically in this situation the controller name often is the actionname and
        // the actionname often become the first value of the parameters.
        
        // lets say, /localhost/contact should route to application controller and contact action.
        // Now the request object will be different before we call the following function (modifyAsRoute).
        // It will look like, controller = contact, action = "". In the next line therefore we modifiy
        // request object which will then become controller = application, action = contact and so on.
        
        $params = array();
        if(self::$request->getActionName() == DEFAULT_ACTION_NAME) {
          // there were no paramters 
        } else {
          $params = array_merge(array(self::$request->getActionName()), 
                    self::$request->getParameters());
        }
        self::$request->modifyAsRouter($r[1], $r[2], $params);
        
        $flag = true;
        break;
      }
    }
    return $flag;
  }

  /**
  * Add special request to the router.
  * @param string $reqularExpression
  * @param string $controllerName
  * @param string $actionName
  */
  public static function addRoute($reqularExpression, $controllerName, $actionName) {
    if(empty($controllerName) || empty($actionName)) {
      throw new Exception('Can  not add special route. Controller name and
                           action name can not be empty');
    }
    
    self::$special_route[] = array($reqularExpression, $controllerName, $actionName);
  }
}
?>