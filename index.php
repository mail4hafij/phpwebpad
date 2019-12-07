<?php
/** -------------------------------------------------------------------------------------*
* Version: 2.0                                                                           *
* License: http://phpwebpad.hafij.com @copyright from 2010                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman (Badal)                                                        *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

header('Content-Type: text/html; charset=utf-8');

/**
 * Start of session
 */
session_start();

/**
 * We are strict with noticeable problem
 * for the script. So we are defining our
 * own error handler for noticeable problem
 * in our script.
 */

// Set the execution time for 5 min.
ini_set('max_execution_time', 300);

// Set display_errors to 1 in the local environment.
ini_set('display_errors', 1);

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
  if(($error['type'] === E_ERROR) || ($error['type'] === ~E_ERROR)) {
    throw new ErrorException("an error has occured in ".$error['file']);
  } else if(($error['type'] === E_USER_ERROR) || ($error['type'] === ~E_USER_ERROR)) {
    throw new UserErrorException("An error has occured in ".$error['file']);
  }
}

# Registering shutdown function
register_shutdown_function('fatalErrorHandler');




/**
* Defining path for this framework.
*/
if(!defined('BIN')) define("BIN", "bin/");
if(!defined('CORE')) define("CORE", "bin/core/");

if(!defined('ORM')) define("ORM", "bin/orm/");
if(!defined('MODEL')) define("MODEL", "application/model/");
if(!defined('LIB')) define("LIB", "lib/");
if(!defined('CONTROLLER')) define("CONTROLLER", "application/controller/");

if(!defined('APPLICATION')) define("APPLICATION", "application/");
if(!defined('VIEW')) define("VIEW", "application/view/");
if(!defined('LAYOUT')) define("LAYOUT", "application/layout/");
if(!defined('ELEMENT')) define("ELEMENT", "application/element/");
if(!defined('ROOT_CONTROLLER_NAME')) define("ROOT_CONTROLLER_NAME", "Controller");

/**
 * Configurable variable.
 * Controller name convention:
 * Allowable name : User, User_settings, Usersettings,
 * Not allowable name: UserSettings, userSettings, User_Settings
 * ---------------------------------------------------
 * This controllername is going to have 'Controller' suffix
 * at the end of its name. So the controller class name become
 * like these: UserController, User_settingsController
 * ---------------------------------------------------
 * File name: The class name should be the file name.
 * e.g: UserController.php, User_settingsController.php
 */
if(!defined('DEFAULT_CONTROLLER_NAME')) define("DEFAULT_CONTROLLER_NAME", "Application");

/**
 * Configurable variable. Any action name should
 * be in small letters. The view file names and the
 * layout file names should also be in small letters.
 */
if(!defined('DEFAULT_ACTION_NAME')) define("DEFAULT_ACTION_NAME", "index");
if(!defined('DEFAULT_LAYOUT_NAME')) define("DEFAULT_LAYOUT_NAME", "layout");

/**
* This index.php file is the starting point of all the requests have been
* maid to this server. All the request will be redirected to this file except
* the requests for the content inside the web file. It will load Request class to handle
* a new request. Then send the request to the Router to be able to pass throw
* controller.
*/
include_once(CORE."Request.php");
include_once(CORE."Router.php");

/**
* Process the server request into a valid request. Then pass the
* recognizable request to the router and start the router in order
* to render the controller.
*/
$request = new Request($_SERVER['REQUEST_URI']);

/**
* Add special route first.
* Then render the request.
*/
Router::addRoute('/^\/howitworks$/i', 'Application', 'howitworks');
Router::addRoute('/^\/download$/i', 'Application', 'download');
Router::render($request);

/**
* autoload for model classes.
* load by demand
* Since we don't want to include our model class
* by ourselves. This __autoload($class) function will
* do the work for us. It will include the required class
* automatically.
* @param string $class
* @exception if the class not found.
*/
function __autoload($class){
  if(!defined('ORM')) define("ORM", "bin/orm/");
  if(!defined('MODEL')) define("MODEL", "application/model/");
  if(!defined('LIB')) define("LIB", "lib/");
  if(!defined('CONTROLLER')) define("CONTROLLER", "application/controller/");
  
  if(file_exists(ORM.$class.'.php') == true) {
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


/**
 * If there is null value then the '=' operator
 * will be replaced with IS NULL or the '!=' operator
 * will be replaced with IS NOT NULL. 
 * Function wise it is same as sprintf 
 * but it will wrap the value.
 * @return query string
 */
function sql() {
  $args = func_get_args();
  $null_arg_pos = array();
  $params = "";
  for($i = 1; $i<count($args); $i++) {
    if(is_null($args[$i])) {
      $null_arg_pos[] = $i - 1;
      $params = $params. 'Database::wrapValue(null), ';
    } else {
      $params = $params. 'Database::wrapValue(\''.addslashes($args[$i]).'\'), ';
    }
  }
  $params = rtrim($params, ", ");
  
  $clause = $args[0];
  foreach($null_arg_pos as $i) {
    $temp = explode("%s", $clause)[$i];
    $new_temp = $temp;
    
    if(strrpos($temp, "!=") !== false) {
      // have to make sure it takes the last != operator.
      $pos1 = strrpos($temp, "!=");
      $pos2 = strrpos($temp, "=") - 1;
      if($pos1 == $pos2) {
        $new_temp = str_lreplace("!=", " IS NOT ", $temp);
      }
    } else if(strrpos($temp, "=") !== false) {
      $new_temp = str_lreplace("=", " IS ", $temp);
    }
    $clause = str_replace($temp, $new_temp, $clause);
  }
  
  
  $php = "";
  if(empty($params)) {
    $php = 'sprintf(\''.$clause.'\');';
  } else {
    $php = 'sprintf(\''.$clause.'\', '.$params.');';
  }
  $str = eval('return '. $php);
  return $str;
}

/***
 * Replace the last occurance of the search 
 */
function str_lreplace($search, $replace, $subject) {
  $pos = strrpos($subject, $search);
  if($pos !== false) {
      $subject = substr_replace($subject, $replace, $pos, strlen($search));
  }
  return $subject;
}

/*** 
 * Callback function for usort.
 */
function date_sort($a, $b) {
  if($a == $b) {
    return 0;
  }
  return ($a < $b) ? -1 : 1;
}

?>