<?php
/** -------------------------------------------------------------------------------------*
* Version: 4.0                                                                           *
* framework: https://github.com/mail4hafij/phpwebpad                                     *
* License: Free to use                                                                   *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

header('Content-type: application/javascript');
$js_files = array(
  "js/phpwebpad.js",
); 

$js = "";
foreach($js_files as $file) {
  $content = file_get_contents($file);
  $js = $js."\n".$content;
  // include_once($file);
}

echo $js;
?>