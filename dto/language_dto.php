<?php

class language_dto {
  public $id;
  public $langcode;
  public $langname;

  public function __construct() {
    
  }
  
  public function newLanguage($id, $langcode, $langname) {
    $instance = new self();
    $instance->id = $id;
    $instance->langcode = $langcode;
    $instance->langname = $langname;
    return $instance;
  }
}

