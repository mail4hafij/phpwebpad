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

header('Content-type: text/css');
$css_files = array(
  "css/style.css",
  "css/notices.css",
  "css/paging.css",
  "css/default.css"
); 

$css = "";
foreach($css_files as $file) {
  $content = file_get_contents($file);
  $pattern = '/url\([^)]*\)/i';
  if(preg_match_all($pattern, $content, $matches)) {
    foreach($matches as $mat) {
      foreach($mat as $m) {
        if(strpos($m, ".png") !== false ||
           strpos($m, ".jpg") !== false || 
           strpos($m, ".jpeg") !== false ||
           strpos($m, ".gif") !== false ||
           strpos($m, ".eot") !== false ||
           strpos($m, ".woff") !== false ||
           strpos($m, ".ttf") !== false ||
           strpos($m, ".svg") !== false ||
           strpos($m, ".woff2") !== false 
        ) {
          $name = $m;
          $name = ltrim($name, "url");
          $name = ltrim($name, "(");
          $name = rtrim($name, ")");
          $name = ltrim($name, "\"");
          $name = rtrim($name, "\"");
          $name = ltrim($name, "\'");
          $name = rtrim($name, "\'");
          
          $replace = $name;
          if(strrpos($name, "/") !== false) {
            $replace = substr($name, strrpos($name, "/") + 1, strlen($name) - strrpos($name, "/"));
          }
          $replace = "assets/$replace";

          // echo $name." with ".$replace; 
          // echo "\n\n";
          $content = str_replace($name, $replace, $content);
        } else {
          // echo $m; 
          // echo "\n\n";
        }
      }
    }
  }
  $css = $css."\n".$content;
  // include_once($file);
} 

echo $css;
?>