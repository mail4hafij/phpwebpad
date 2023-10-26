<?php
class TimeMachineTest extends \PHPUnit\Framework\TestCase {
  
  protected function setUp() : void {
  }
  
  public function testTimeMachine() {
    $datetime = TimeMachine::getDateTime();
    
    TimeMachine::$TIME_TRAVEL = 30;
    $future = new DateTime(TimeMachine::getDateTime());
    $this->assertEquals($datetime, 
      $future->sub(new DateInterval("P30D"))->format("Y-m-d H:i:s"));
    
    TimeMachine::$TIME_TRAVEL = -30;
    $past = new DateTime(TimeMachine::getDateTime());
    $this->assertEquals($datetime, 
      $past->add(new DateInterval("P30D"))->format("Y-m-d H:i:s"));
  }
}
?>