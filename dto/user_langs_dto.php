<?php
/**
 * Class for UserLangs.
 * Contains pairs of user-languages (meaning that  the user spèaks that language)
 * 
 * int id:  UserLang identifier
 * int user_id:  User ID
 * int lang_id: Lang ID
 */
class user_langs_dto {
  
  public $id;
  public $user_id;
  public $lang_id;

  public function __construct() {
    
  }
  
  /**
   * Builds a new UserLang object
   * 
   * @param int $user_id User ID
   * @param int $lang_id Lang ID
   * @return \self
   */
  public function newUserLang($user_id, $lang_id){
    $instance = new self();
    $instance->user_id = $user_id;
    $instance->lang_id = $lang_id;
    
    return $instance;
  }
  
}


