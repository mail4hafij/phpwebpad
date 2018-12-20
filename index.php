<?php
/** -------------------------------------------------------------------------------------*
* Version: 1.2                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

/**
 * Start of session
 */
session_start();

/**
 * We are strict with noticeable problem. We are defining our
 * own error handler.
 */
function error_notice($num, $str, $file, $line) {
  echo("$str in $file line $line");
}
set_error_handler("error_notice", E_NOTICE);

/**
* Defining path for this framework.
*/
if(!defined('BIN')) define("BIN", "bin/");
if(!defined('CORE')) define("CORE", "bin/core/");
if(!defined('ORM')) define("ORM", "bin/orm/");
if(!defined('MODEL')) define("MODEL", "application/model/");
if(!defined('LIB')) define("LIB", "lib/");
if(!defined('APPLICATION')) define("APPLICATION", "application/");
if(!defined('CONTROLLER')) define("CONTROLLER", "application/controller/");
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
 * at the end of its name.So the controller class name become
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
* This index.php file is the starting point for all the requests are
* maid to this server. All the request will be redirected to this file except
* the requests for the content inside the web file. It will load Request class to handle
* a new request. Then send the request to the Router to be able to pass through
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
  if(!defined('CONTROLLER')) define("CONTROLLER", "application/controller/");
  if(!defined('LIB')) define("LIB", "lib/");

  if(file_exists(ORM.$class.'.php') == true) {
    include_once(ORM.$class.'.php');
  } else if(file_exists(MODEL.$class.'.php') == true) {
    include_once(MODEL.$class.'.php');
  } else if(file_exists(CONTROLLER.$class.'.php') == true) {
    include_once(CONTROLLER.$class.'.php');
  } else if(file_exists(LIB.$class.'.php') == true) {
    include_once(LIB.$class.'.php');
  } else {
    throw new Exception('Class '.$class.' can not be found.');
  }
}

/**
 * Same as sprintf but it will wrap the value.
 * @return query string
 */
function sql() {
  $args = func_get_args();
  $params = "";
  for($i = 1; $i<count($args); $i++) {
    $params = $params. 'Database::wrapValue(\''.$args[$i].'\'), ';
  }
  $params = rtrim($params, ", ");
  if(empty($params)) $php = 'sprintf(\''.$args[0].'\');';
  else $php = 'sprintf(\''.$args[0].'\', '.$params.');';
  $str = eval('return '. $php);
  return $str;
}
?>