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


class Helper {
/**
 * If there is null value then the '=' operator
 * will be replaced with IS NULL or the '!=' operator
 * will be replaced with IS NOT NULL. 
 * Function wise it is same as sprintf 
 * but it will wrap the value.
 * @return query string
 */
  public static function sql() {
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
          $new_temp = self::str_lreplace("!=", " IS NOT ", $temp);
        }
      } else if(strrpos($temp, "=") !== false) {
        $new_temp = self::str_lreplace("=", " IS ", $temp);
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

  public static function clean($text, $allowed_tags = null) {
    return strip_tags(trim($text), $allowed_tags);
  }

  // if you have allowed a tag in clean funciton then
  // icho will not work.
  public static function icho($text, $make_url_clickable = true) {
    if($make_url_clickable) {
      $pattern = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
      $text = preg_replace($pattern, '<a href="http$2://$4">$0</a>', $text);
    }
    return nl2br($text);
  }

  // The database object properties can not be checked
  // with php empty method directly. That is why simply
  // passing that value and checking with the same php
  // empty method.
  public static function isEmpty($val) {
    return empty($val);
  }

  // For php 7.4 version
  public static function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle; 
  }
  
  /***
   * Replace the last occurance of the search 
   */
  public static function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if($pos !== false) {
      $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
  }

  /*** 
   * Callback function for usort.
   */
  public static function date_sort($a, $b) {
    if($a == $b) {
      return 0;
    }
    return ($a < $b) ? -1 : 1;
  } 
}
?>
