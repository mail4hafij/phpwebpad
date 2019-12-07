<?php
/** -------------------------------------------------------------------------------------*
* Version: 2.0                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

abstract class Controller {
  private $view_var = array();
  private $layout_var = array();
  private $view = DEFAULT_ACTION_NAME;
  private $layout = DEFAULT_LAYOUT_NAME;
  private static $log = array();

  abstract function actions();
  abstract function jsonActions();
  abstract function catchUnAllowedActions($action_name);
  abstract function beforRender($action_name, $controller_name);
  abstract function afterRender($action_name, $controller_name);

  function setLayout($layout = null){ $this->layout = $layout; }
  private function getLayout(){ return $this->layout; }
  function setLayoutVar($var_name, $value){ $this->layout_var[$var_name] = $value; }
  private function getLayoutVar(){ return $this->layout_var; }
  function setView($view = null){ $this->view = $view; }
  private function getView(){ return $this->view; }
  function setViewVar($var_name, $value){ $this->view_var[$var_name] = $value; }
  private function getViewVar(){ return $this->view_var; }

  /**
  * Take the controller name then set all the view variable
  * available to the page from the controller. Find out the
  * view file and layout file if it has any. If the both file
  * exist then put the view file content inside the layout
  * file and render it to the browser. Otherwise only view
  * file or layout file will be rendered depends on which
  * file is available.
  * @Exception if the view file can not be found if it needs
  * to be rendered.
  * @Exception if the layout file can not be found if it needs
  * to be rendered.
  * @param string $controller_name
  * return void.
  */
  function renderView($controller_name) {
    $controller_name = strtolower($controller_name);
    if($this->getView() == null && $this->getLayout() == null) {
      return;
    }
    
    $view_file = VIEW.$controller_name."/".$this->getView().".php";
    $layout_file = LAYOUT.$this->getLayout().".php";

    if($this->getView() != null && !file_exists($view_file)) {
      throw new Exception("FATAL: View file ".$view_file." can not be found.");
    }
    
    if($this->getLayout() != null && !file_exists($layout_file)) {
      throw new Exception("FATAL: Layout file ".$layout_file." can not be found.");
    }
    
    include_once(CORE."Minifier.php");
    
    if(file_exists($layout_file) && file_exists($view_file)) {  
      ob_start();
      extract($this->getViewVar(), EXTR_PREFIX_SAME, 'view_');
      include($view_file);
      $view_file_content = ob_get_contents();
      ob_end_clean();
      
      ob_start();
      $this->setLayoutVar("__VIEW__", $view_file_content);
      extract($this->getLayoutVar(), EXTR_PREFIX_SAME, 'layout_');
      include($layout_file);
      $layout_file_content = Minifier::minify(ob_get_contents());
      ob_end_clean();
      
      echo($layout_file_content);
      
    } else if(file_exists($view_file) && $this->getLayout() == null) {
      ob_start();
      extract($this->getViewVar(), EXTR_PREFIX_SAME, 'view_');
      include($view_file);
      $view_file_content = Minifier::minify(ob_get_contents());
      ob_end_clean();
      
      echo($view_file_content);
      
    } else if(file_exists($layout_file) && $this->getView() == null) {
      ob_start();
      extract($this->getLayoutVar(), EXTR_PREFIX_SAME, 'layout_');
      include($layout_file);
      $layout_file_content = Minifier::minify(ob_get_contents());
      ob_end_clean();
      
      echo($layout_file_content);
    }
  }


  /**
  * Check if the action is allowed or not. If so then
  * set the view file with this action name. And pass
  * all the elements from parameters array as sequential
  * parameters to that action. If the action is not
  * allowed it calls catchUnallowedActions function
  * with the action name as it's parameter. We can have
  * two kind of action. If the action is json action
  * in that case no view file and no layout file will be
  * set.
  * @Exception if the action can be found in both normal and
  * json action.
  * @param string $action_name
  * @param array $parameters
  */
  function renderAction($action_name, $parameters) {
    $is_action = $this->isActionAllowed($action_name, false);
    $is_json_action = $this->isActionAllowed($action_name, true);
    if($is_action == true && $is_json_action == true) {
      throw new Exception("FATAL: Acation can not be both normal and ajax.");
    }
    
    if($is_action) {
      $this->setView($action_name);
      $param = "";
      foreach($parameters as $key=>$value) {
        $param = $param."\$parameters[$key],";
      }
      $param = rtrim($param,",");
      $php = "\$this"."->".$action_name."(".$param.");";
      eval($php);
      
    } else if($is_json_action) {
      $param = "";
      foreach($parameters as $key=>$value) {
        $param = $param."\$parameters[$key],";
      }
      $param = rtrim($param,",");
      $php = "\$this"."->".$action_name."(".$param.");";
      include_once(CORE."JSONResponse.php");
      $json = eval('return '.$php);
      
      echo $json->getResponse();
      $this->setView(null);
      $this->setLayout(null);
      
    } else {
      $this->catchUnAllowedActions($action_name);
    }
  }

  /**
   * Check if the action name is allowed to render.
   * @Exception if the action name is empty.
   * @param string $action_name
   * @param boolean $json_action
   * @return boolean
   */
  private function isActionAllowed($action_name, $json_action = false) {
    if(empty($action_name)) {
      throw new Exception('FATAL: The action name is empty.');
    }
    
    $allowable = false;
    $all_actions = array();
    if($json_action) {
      $all_actions = $this->jsonActions();
    } else {
      $all_actions = $this->actions();
    }
    
    foreach($all_actions as $action) {
      if($action == $action_name) {
        $allowable = true;
        break;
      }
    }
    return $allowable;
  }

  /**
  * includes a element file.
  * @Exception if the file is not found or the path is empty
  * @param string $path
  * @param array $data
  * @return string
  */
  public static function renderElement($path, $data = array()){
    if(empty($path)) {
      throw new Exception('FATAL: No path to the element file has been given.');
    }
      
    $element_file = ELEMENT.$path.".php";
    if(!file_exists($element_file)) {
      throw new Exception('FATAL: Element '. $element_file .' can not be found.');
    }
    
    // Do not need minification.
    // Because elements are part of layout or view page.
    ob_start();
    extract($data, EXTR_PREFIX_SAME, 'element_');
    include($element_file);
    $element_content = ob_get_contents();
    ob_end_clean();
    return $element_content;
  }

  /**
   * Redirect to new url.
   * @Exception if the path is not valid or empty
   * @param string $path
   */
  public static function redirectAndExit($path){
    if(empty($path)) {
      throw new Exception("FATAL: No path was found to redirect.");
    }
    header('Location: '.$path);
  }

  /**
   * Add log to the controller.
   * @param string $log
   */
  public static function addLog($log) {
    self::$log[] = $log;
  }

  /**
   * Get all the log messages.
   * @return string
   */
  public static function getLog() {
    return self::$log;
  }
}
?>