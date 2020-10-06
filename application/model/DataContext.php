<?php
class DataContext {
  private static $database = null;
  
  public static function getDatabase() {
    if (self::$database == null) {
      // localhost
      self::$database = new Database('localhost', 'database_name', 'root', '');
    }
    return self::$database;
  }
  
  public static function init() {
    // Set alter table mode.
    $database = DataContext::getDatabase();
    $database->setAlterTable(true);
    
    // Build all the table here.
    
    $database->startTransaction();
    try {
      
      // Make initial entries.
      
      if($database->isInTransaction()) {
        $database->commit();
      }
    } catch (Exception $e) {
      if($database->isInTransaction()) {
        $database->rollback();
      }
      throw $e; 
    }
  }
}
