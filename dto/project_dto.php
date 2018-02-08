<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/language_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");

class project_dto {
  public $id;
  public $name;
  public $source_lang;
  public $target_lang;
  public $description;
  public $creation_date;
  public $active;
  public $owner;
  
  public $source_lang_object;
  public $target_lang_object;
  public $owner_object;
  
  public function __construct(){
    $this->source_lang_object = new language_dto();
    $this->target_lang_object = new language_dto();
    $this->owner_object = new user_dto();
  }
  
  public function newProject($id, $name, $source_lang, $target_lang, $description, $active){
      $instance = new self();
      $instance->id = $id;
      $instance->name = $name;
      $instance->source_lang = $source_lang;
      $instance->target_lang = $target_lang;
      $instance->description = $description;
      $instance->active = $active;
      return  $instance;
  }
}