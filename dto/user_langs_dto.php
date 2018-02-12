<?php

class user_langs_dto {
  public $id;
  public $user_id;
  public $lang_id;

  public function __construct() {
    
  }
  
  public function newUserLang($user_id, $lang_id){
    $instance = new self();
    $instance->user_id = $user_id;
    $instance->lang_id = $lang_id;
    
    return $instance;
  }
  
}


