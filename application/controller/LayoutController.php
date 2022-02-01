<?php
class LayoutController extends Controller {
  /**
   * Start implementation of Controller class
   */
  public function actions() {
    $actions = array('loadbalancer');
    return $actions;
  }
  
  public function jsonActions() {
    return array();
  }
  
  public function catchUnAllowedActions($action_name) {
    throw new UnAuthorizedException("Action $action_name is not allowed.");
  }
  
  public function beforRender($action_name, $controller_name) {
    if($action_name == "index" && $controller_name == "Application") {
      // Init database
      // TimeMachine::$TIME_TRAVEL = 0;
      DataContext::init(false);
    }
    
    // Init settings
    InfoKeeper::getSettings();
    $this->setLayout('layout');
  }
  
  public function afterRender($action_name, $controller_name) {

  }
  /**
   * End
   */

  public function loadbalancer() {
    $this->setLayout(null);
    $this->setViewVar("browser", WebContext::getUserBrowser());
  }
  
}
?>