<?php

/**
 * Class for Project objects
 * Contains information related to a project
 * 
 * int id: Project ID
 * string name: Project name
 * int source_lang: Source language ID
 * int target_lang: Target language ID
 * string description: Description of the project
 * string creation_date: Creation date
 * boolean active: Active status of the project (active/deactivated)
 * int owner: Owner of the project (user ID)
 * object source_lang_object: Language object for the source language
 * object target_lang_object: Language object for the target language
 * object owner_object: User object for the owner of the project
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/language_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");

class project_dto {
  public $id;
  public $name;
  public $description;
  public $creation_date;
  public $active;
  public $owner;
  
  public $owner_object;
  
  public function __construct(){
    $this->source_lang_object = new language_dto();
    $this->target_lang_object = new language_dto();
    $this->owner_object = new user_dto();
  }
  
  /**
   * Builds a new project object
   * 
   * @param int $id Project ID
   * @param string $name Project name
   * @param int $source_lang Source language ID
   * @param int $target_lang Target language ID
   * @param string $description Description of the project
   * @param boolean $active Active status (active/deactivated)
   * @return \self
   */
  public function newProject($id, $name, $description, $active){
      $instance = new self();
      $instance->id = $id;
      $instance->name = $name;
      $instance->description = $description;
      $instance->active = $active;
      return  $instance;
  }
}