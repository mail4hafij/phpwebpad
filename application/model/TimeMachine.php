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

class TimeMachine {
  public static $TIME_ZONE = 1;   // Sweden
  public static $ADJ_HOUR = 1;    // Daylight saving
  public static $TIME_TRAVEL = 0; // Days (pos/neg) we want to travel
  
  private static function getAdjustedTime() {
    return time() + (self::$TIME_TRAVEL * 24 * 60 * 60) + 
      (3600 * (self::$TIME_ZONE + date("I") + self::$ADJ_HOUR));
  }
  
  public static function getDate() {
    return gmdate('Y-m-d', self::getAdjustedTime());
  }
  
  public static function getTime() {
    return gmdate('H:i', self::getAdjustedTime());
  }
  
  public static function getDateTime() {
    return gmdate('Y-m-d H:i:s', self::getAdjustedTime());
  }
  
  public static function getYear() {
    return gmdate('Y', self::getAdjustedTime());
  }
  
  public static function getTomorrowDate() {
    return gmdate('Y-m-d', self::getAdjustedTime() + (1 * 24 * 60 * 60));
  }
  
  public static function getYesterdayDate() {
    return gmdate('Y-m-d', self::getAdjustedTime() - (1 * 24 * 60 * 60));
  }
    
  public static function getHumanDay($date) {
    $today = TimeMachine::getDateTime();
    $date = strtotime($date);

    $datediff = $today - $date;
    $difference = floor($datediff/(60*60*24));
    if($difference == 0) {
      return 'Today';
    } else if($difference == 1) {
        return 'Tomorrow';
    } else if($difference == 2) {
        return 'Day After Tomorrow';
    } else if($difference > 2) {
        return 'Future';
    } else if($difference < -90) {
        return 'Long Back';
    } else if($difference < -60) {
        return '2 Months Back';
    } else if($difference < -30) {
        return '1 Month Back';
    } else if($difference < -20) {
        return '3 Weeks Back';
    } else if($difference < -13) {
        return '2 Weeks Back';
    } else if($difference < -6) {
        return 'Last Week';
    } else if($difference < -5) {
        return '6 Days Back';
    } else if($difference < -4) {
        return '5 Days Back';
    } else if($difference < -3) {
        return '4 Days Back';
    } else if($difference < -2) {
        return '3 Days Back';
    } else if($difference < -1) {
        return '2 Days Back';
    } else if($difference < 0) {
        return 'Yesterday';
    }  
  }
    
}

?>
