<?php

/**
 * Class for Language objects.
 * Contains language information 
 * 
 * int id: Language ID
 * string langcode: Language code
 * string langname: Language name
 */
class language_dto {
  public $id;
  public $langcode;
  public $langname;

  public function __construct() {
    
  }
  
  /**
   * Builds a new language object
   * 
   * @param int $id Language id
   * @param string $langcode Language code
   * @param string $langname Language name
   * @return \self 
   */
  public function newLanguage($id, $langcode, $langname) {
    $instance = new self();
    $instance->id = $id;
    $instance->langcode = $langcode;
    $instance->langname = $langname;
    return $instance;
  }
}

