<?php

class language_dto {
  public $id;
  public $langcode;
  public $langname;

  public function __construct() {
    
  }
  
  public function newLanguage($langcode, $langname) {
    $instance = new self();
    $instance->langcode = $langcode;
    $instance->langname = $langname;
    return $instance;
  }
}

