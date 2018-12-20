<?php
class Mail {
  private $to;
  private $from;
  
  public function  __construct($to, $from) {
    if(!self::validateEmail($to)) {
      throw new Exception("The receiver email address is not valid");
    }
    
    if(!self::validateEmail($from)) {
      throw new Exception("The sender email address is not valid");
    }
    
    $this->to = $to;
    $this->from = $from;
  }

  public static function validateEmail($email) {
    if(preg_match('/.*\@.*\..*/i', $email))
      return true;
    return false;
  }
  
  /**
   *
   * @param <type> $subject
   * @param string $msg
   * @return <Boolean> $isSent
   */
  public function sendEmail($subject, $msg) {
    $newLine = "\r\n";
    $headers = "MIME-Version: 1.0" . $newLine;
    $headers .= "Content-Type: text/html;charset=utf-8" . $newLine;
    $headers .= "To: <$this->to>" . $newLine;
    $headers .= "From: $this->from" . $newLine;
    return mail($this->to, '=?utf-8?B?'.base64_encode($subject).'?=', $msg, $headers);
  }
}
?>