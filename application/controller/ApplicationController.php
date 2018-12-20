<?php
class ApplicationController extends LayoutController {
  /**
  * Start of implementing abstract methods of the LayoutController class
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