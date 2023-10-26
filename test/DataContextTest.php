<?php
class DataContextTest extends \PHPUnit\Framework\TestCase {
  
  protected function setUp() : void {
  }
  
  public function testDataContext() {
    DataContext::$test_db = true;
    $database = DataContext::getDatabase();
    $settings = InfoKeeper::getSettings();
    $this->assertEquals($database->getDbName(), $settings->db_test_name);
  }
}
?>