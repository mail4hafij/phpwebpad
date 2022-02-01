<?php
/** -------------------------------------------------------------------------------------*
* Version: 3.0                                                                           *
* framework: https://github.com/mail4hafij/phpwebpad                                     *
* License: Free to use                                                                   *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

// Start the session.
session_start();

// Set the execution time for 5 min.
ini_set('max_execution_time', 300);

// Set display_errors to 1 in the local environment.
ini_set('display_errors', 1);


/**
 * START Error Handling
 * --------------------
 * We are strict with noticeable problem
 * for the script. So we are defining our
 * own error handler for noticeable problem
 * in our script.
 */

// Predefined exceptions
class NoticeException               extends ErrorException {}
class UserNoticeException           extends ErrorException {}
class WarningException              extends ErrorException {}
class UserWarningException          extends ErrorException {}

class UserErrorException            extends ErrorException {}
class UnAuthorizedException         extends ErrorException {}

// Error handler functions.
function error_notice($num, $str, $file, $line) {
  throw new NoticeException("$str in $file line $line");
}
function error_user_notice($num, $str, $file, $line) {
  throw new UserNoticeException("$str in $file line $line");
}
function error_warning($num, $str, $file, $line) {
  throw new WarningException("$str in $file line $line");
}
function error_user_warning($num, $str, $file, $line) {
  throw new UserWarningException("$str in $file line $line");
}

set_error_handler("error_notice", E_NOTICE);
set_error_handler("error_notice", ~E_NOTICE);
set_error_handler("error_user_notice", E_USER_NOTICE);
set_error_handler("error_user_notice", ~E_USER_NOTICE);

set_error_handler("error_warning", E_WARNING);
set_error_handler("error_warning", ~E_WARNING);
set_error_handler("error_user_warning", E_USER_WARNING);
set_error_handler("error_user_warning", ~E_USER_WARNING);

/**
 * Handling fatal error
 * @return void
 */
function fatalErrorHandler() {
  # Getting last error
  $error = error_get_last();

  # Checking if last error is a fatal error 
  if(isset($error) && ($error['type'] === E_ERROR || $error['type'] === ~E_ERROR)) {
    throw new ErrorException("an error has occured in ".$error['file'].":".$error['message']);
  } else if(isset($error) && ($error['type'] === E_USER_ERROR || $error['type'] === ~E_USER_ERROR)) {
    throw new UserErrorException("An error has occured in ".$error['file'].":".$error['message']);
  }
}

# Registering shutdown function
register_shutdown_function('fatalErrorHandler');

/**
 * END Error handling
 */


/**
* START Defining path for this framework
* --------------------------------------
*/
function phpwebpad_name_define() {
  if(!defined('APPLICATION')) define("APPLICATION", "application/");
  if(!defined('VIEW')) define("VIEW", "application/view/");
  if(!defined('LAYOUT')) define("LAYOUT", "application/layout/");
  if(!defined('ELEMENT')) define("ELEMENT", "application/element/");
  if(!defined('ROOT_CONTROLLER_NAME')) define("ROOT_CONTROLLER_NAME", "Controller");

  /**
   * Configurable variable.
   * Controller name convention: The controllername is going to have 
   * 'Controller' suffix at the end of its name. 
   * i.e., UserController.php, User_settingsController.php
   */
  if(!defined('DEFAULT_CONTROLLER_NAME')) define("DEFAULT_CONTROLLER_NAME", "Application");

  /**
   * Configurable variable. 
   * Action name convention: Any action name should be in small letters. 
   * The view file names and the layout file names should also be in 
   * small letters.
   */
  if(!defined('DEFAULT_ACTION_NAME')) define("DEFAULT_ACTION_NAME", "index");
  if(!defined('DEFAULT_LAYOUT_NAME')) define("DEFAULT_LAYOUT_NAME", "layout");
}

function phpwebpad_class_define() {
  if(!defined('CORE')) define("CORE", "bin/core/");
  if(!defined('ORM')) define("ORM", "bin/orm/");
  if(!defined('MODEL')) define("MODEL", "application/model/");
  if(!defined('LIB')) define("LIB", "lib/");
  if(!defined('CONTROLLER')) define("CONTROLLER", "application/controller/");
}
/**
 * END Defining path for this framework
 */


/**
* START autoload classes
* ----------------------
* @param string $class
* @exception if the class not found.
*/
function __autoload($class){
  // define all the path.
  phpwebpad_class_define();
  
  if(file_exists(CORE.$class.'.php') == true) {
    include_once(CORE.$class.'.php');
  } else if(file_exists(ORM.$class.'.php') == true) {
    include_once(ORM.$class.'.php');
  } else if(file_exists(MODEL.$class.'.php') == true) {
    include_once(MODEL.$class.'.php');
  } else if(file_exists(LIB.$class.'.php') == true) {
    include_once(LIB.$class.'.php');
  } else if(file_exists(CONTROLLER.$class.'.php') == true) {
    include_once(CONTROLLER.$class.'.php');
  } else {
    throw new Exception('Class '.$class.' can not be found.');
  }
}
// Manually include the helper functions.
include_once("bin/Helper.php");
/**
 * END autoload classes
 */


 /**
* START phpwebpad
* ---------------
* This index.php file is the starting point of all the requests have been
* maid to this server. Process the server request into a valid request. 
* Then pass the request to the router in order to render the controller.
*/
phpwebpad_name_define();
$request = new Request($_SERVER['REQUEST_URI']);

/**
* Add special route first.
* Then render the request.
*/
Router::addRoute('/^\/howitworks$/i', 'Application', 'howitworks');
Router::addRoute('/^\/download$/i', 'Application', 'download');
Router::render($request);
/**
 * END phpwebpad
 */
?>