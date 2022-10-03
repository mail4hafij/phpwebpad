<?php
class ApplicationController extends LayoutController {
  /**
  * Start implementation of Controller class
  */
  public function actions() {
    $actions = array('index');
    return $actions;
  }
  
  public function jsonActions() {
    return array();
  }
  /**
  * End
  */
	
  public function index() {
    
  }
}
?>