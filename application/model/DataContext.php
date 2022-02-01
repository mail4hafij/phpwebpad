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

class DataContext {
  private static $database = null;
  public static $test_db = false;

  public static function getDatabase() {
    if (self::$database == null) {
      if(WebContext::isLocalhost()) {
        // localhost
        if(self::$test_db) {
          $settings = InfoKeeper::getSettings();
          self::$database = new Database($settings->db_test_host, 
            $settings->db_test_name, $settings->db_test_user, 
            $settings->db_test_pass);
          
        } else {
          self::$database = new Database('localhost', 'phpwebpad', 'root', '');
        }
      } else {
        // Server
      } 
    }
    return self::$database;
  }
  
  public static function init($insert_test_data = false) {
    self::createAllTables();
    if(WebContext::isLocalhost() && $insert_test_data) {
      self::deleteAllImages();  
      self::truncateAllTables();
      self::insertTestData();
    }
  }

  public static function createAllTables() {
    // Set alter table mode.
    $database = DataContext::getDatabase();
    $database->setAlterTable(true);

    // Build all the tables here.
    
  }

  public static function insertTestData() {
    $database = DataContext::getDatabase();

    $local_commit = false;
    if(!$database->isInTransaction()) {
      $database->startTransaction();
      $local_commit = true;
    }

    try {

      // Insert test data.
      
      if($local_commit) {
        $database->commit();
      }
    } catch (Exception $e) {
      if($local_commit) {
        $database->rollback();
      }
      throw $e; 
    }
  }

  public static function truncateAllTables() {
    $database = DataContext::getDatabase();
    $table_list = $database->getAllTableNames();
    foreach($table_list as $table_name) {
      $t = new $table_name();
      $database->truncateTable($t->getTableDefinition());
    }
  }

  public static function deleteAllImages() {
    $path_list = array();
    $keep_files = array();

    foreach($path_list as $path) {
      $file_list = glob($path);
      foreach($file_list as $file) { 
        if(is_file($file)) {
          if(!in_array(basename($file), $keep_files)) {
            unlink($file);
          }
        }
      }
    }
  }

}
