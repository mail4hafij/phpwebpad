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


// ROOT FUNCTIONS! 

/**
 * START Helper functions
 * ----------------------
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

function clean($text, $clean_html = true, $keep_newline = true) {
  $text = trim($text);
  if($clean_html) {
    if($keep_newline) {
      return nl2br(strip_tags($text));
    } else {
      return strip_tags($text);
    }
  } else {
    if($keep_newline) {
      return nl2br($text);
    } else {
      return $text;
    }
  }
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

/**
 * END Helper function
 */
?>