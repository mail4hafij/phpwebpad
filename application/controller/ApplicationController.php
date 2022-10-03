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
    return array('submitBugReport');
  }
  /**
  * End
  */
	
  public function index() {
    
  }

  public function submitBugReport() {
    $json = new JSONResponse();
    $json->setVar('showmsg', 'showmsg_bug_report');
    
    if(!isset($_POST["problem"])) {
      $json->setVar('error', "error in input data");
      return $json;
    }
    
    try {
      $report = Helper::clean($_POST["problem"]);
      
      if(!empty($report)) {
        // Email admin
      }
      
            
    } catch(Exception $e) {
      $json->setVar("error", $e->getMessage());
      return $json;
    }

    $json->setVar("url", "current");
    InfoKeeper::setCurrentStatus("Thank you for reporting. We will look at this as soon as possible.");
    return $json;
  }
}
?>