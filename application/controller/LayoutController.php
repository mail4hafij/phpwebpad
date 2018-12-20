<?php
class LayoutController extends Controller {
  /**
   * Start of implementing abstract methods of the LayoutController class
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
    $this->setLayout('layout');
  }
  
  public function afterRender($action_name, $controller_name) {

  }
  /**
   * End
   */

  public function loadbalancer() {
    $this->setLayout(null);
    $this->setViewVar("browser", Config::getUserBrowser());
  }
  
}
?>