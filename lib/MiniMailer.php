<?php
/**
 * MiniMailer.php
 *
 * Very simple mailer for PHP.
 *
 * @author Florian Plank, Polarblau
 * @version 1.0
 */
 
class MiniMailer {
                       
  private $to                 = null;
  private $from               = null;
  private $subject            = null;
  private $body               = null;
  private $headers            = null;
  private $validators         = array();
  private $errors             = array();
  private $form_field_mapping = array();
  
  
  function __construct($fields = array()) {
    foreach ($fields as $key => $value) {
      $setter = "set_" . $key;
      $this->$setter($value);
    }
  }
  
  public function use_form_fields($fields = array()) {
    if (isset($_REQUEST)) {
      foreach ($fields as $key => $value) {
        $setter = "set_" . $key;
        $this->$setter($_REQUEST[$value]);
      }
    }
    $this->form_field_mapping = $fields;
  }
  
  public function set_to($to) {
    $this->to = $to;
  }
  
  public function set_from($from) {
    $this->from = $from;
  }
  
  public function set_subject($subject) {
    $this->subject = $subject;
  }
  
  public function set_body($body) {
    $this->body = $body;
  }
  
  public function add_header($header) {
    $this->headers .= $header;
  }
  
  public function add_validator($field, $validation) {
    if (!isset($this->validators[$field])) {
      $this->validators[$field] = array($validation);
    } else {
      array_push($this->validators[$field], $validation);
    }
  }
  
  public function validate() {
    $this->errors = array();
    foreach ($this->validators as $field => $validators) {
      foreach ($validators as $method) {
        $validator = "validate_" . $method;
        $error = $this->$validator($this->$field);
        if (isset($error)) {
          if (!isset($this->errors[$field])) {
            $this->errors[$field] = array();
          }
          array_push($this->errors[$field], $error);
        }
      }
    }
    return count($this->errors) == 0;
  }
  
  public function deliver() {
    if (!$this->validate()) {
      return false;
    } else {
      $this->add_header('From: '.$this->from."\r\n");
      $this->add_header('Reply-To: '.$this->from."\r\n");
      $this->add_header('Return-Path: '.$this->from."\r\n");
      $this->add_header('X-mailer: PHP MiniMailer 1.0'."\r\n");
      return mail($this->to, $this->subject, $this->body, $this->headers);
    }
  }
  
  public function get_errors() {
    return $this->errors;
  }
  
  public function get_form_errors() {
    $form_field_errors = array();
    foreach ($this->form_field_mapping as $field => $form_field) {
      if (count($this->errors[$field]) > 0) {
        $form_field_errors[$form_field] = $this->errors[$field];
      }
    }
    return $form_field_errors;
  }
  
  // PRIVATE 
  
  private function validate_presence($field){
    if (!isset($field) || strlen($field) <= 0) {
      return "missing";
    }
  }

  // http://www.linuxjournal.com/article/9585
  private function validate_email($email) {
     $is_valid = true;
     $at_index = strrpos($email, "@");
     if (is_bool($at_index) && !$at_index) {
       $is_valid = false;
     } else {
      $domain = substr($email, $at_index+1);
      $local = substr($email, 0, $at_index);
      $local_len = strlen($local);
      $domain_len = strlen($domain);
      if ($local_len < 1 || $local_len > 64) {
        $is_valid = false;
      } else if ($domain_len < 1 || $domain_len > 255) {
        $is_valid = false;
      } else if ($local[0] == '.' || $local[$local_len-1] == '.') {
        $is_valid = false;
      } else if (preg_match('/\\.\\./', $local)) {
        $is_valid = false;
      } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
        $is_valid = false;
      } else if (preg_match('/\\.\\./', $domain)) {
        $is_valid = false;
      } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
        if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
          $is_valid = false;
        }
      }
      if ($is_valid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
        $is_valid = false;
      }
    }
    return $is_valid ? null : "invalid email address";
  }
  
}
?>