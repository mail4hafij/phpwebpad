<?php
/**
 * Mohammad Hafijur Rahman
 */
class FileUploader {
  var $upload_dir;
  var $type;

  public function  __construct($upload_dir, $type) {
    $this->upload_dir = $upload_dir;
    $this->type = $type;
  }

  /**
   *
   * @param <type> $file_variable_name
   * @param <type> $propose_file_name
   * @return string $url
   */
  public function uploadFile($file_variable_name, $propose_file_name = '') {
    if(empty($propose_file_name))
      throw new Exception ('File name can not be empty');

    $url = null;
    $file_name = $_FILES[$file_variable_name]['name'];
    foreach($this->type as $t) {
      $file_type = ".".$t;
      $file_name = str_replace(" ", "", $file_name);
      if(stristr($file_name,$file_type)) {
        $upload_file = $this->upload_dir.$file_name;
        if($propose_file_name != '') {
          $upload_file = $this->upload_dir.$propose_file_name.$file_type;
        }
        
        if(move_uploaded_file($_FILES[$file_variable_name]['tmp_name'], $upload_file)) {
          chmod($upload_file, 0644);
          $url = $upload_file;
          break;
        }
      }
    }
    if($url == null) {
      throw new Exception('File type not supported');
    }
    return $url;
  }
}

/*
$type = array('jpg', 'jpeg', 'gif', 'bmp');
$file = new FileUploader("images/members/", $type);
$status = $file -> uploadFile('user_info_img', '');
*/
?>