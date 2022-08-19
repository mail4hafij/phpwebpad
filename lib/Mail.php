<?php

# ========================================================================#
#  Author: Mohammad Hafijur Rahman
#  Version:	2.0
#  Date: 01-01-2021
#  Purpose: Send email using php mail function
#  Requires : Requires PHP5
# ========================================================================#

class Mail {
  private $to;
  private $from;
  private $domain_name;
  
  public function  __construct($to, $from, $domain_name) {
    if(empty($domain_name)) {
      throw new Exception("Site name can not be empty");
    }
    
    if(!self::validateEmail($to)) {
      throw new Exception("The receiver email address is not valid");
    }
    
    if(!self::validateEmail($from)) {
      throw new Exception("The sender email address is not valid");
    }
    
    $this->to = $to;
    $this->from = $from;
    $this->domain_name = $domain_name;
  }

  public static function validateEmail($email) {
    if(preg_match('/.*\@.*\..*/i', $email)){
      return true;
    }
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
    $headers .= 'Content-Transfer-Encoding: base64' . $newLine;
    $headers .= 'Date: ' . date("r") . $newLine;
    $headers .= "From: $this->from" . $newLine;
    $headers .= "Reply-To: $this->from" . $newLine;
    $headers .= 'Message-ID: ' . sprintf("<%s.%s@%s>",
      time(), md5($this->from . $this->to), $this->domain_name) . $newLine;
    
    $subject = '=?utf-8?B?'.base64_encode($subject).'?=';
    $encoded_msg = base64_encode($msg);
    
    return mail($this->to, $subject, $encoded_msg, $headers);
  }
  
}
?>