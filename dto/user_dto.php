<?php
/**
 * Class for Users.
 * Contains the information related to a user 
 * 
 * int id: User ID
 * string name: User's name
 * string email: User's email
 * string creation_date: Date when the account was created
 * string role: User's role
 * string password: User's encoded password
 * boolean active: User status (active/deactivated)
 * array langs: Langs spoken by the user
 */
class user_dto{
  public $id;
  public $name;
  public $email;
  public $creation_date;
  public $role;
  public $password;
  public $active;
  public $langs;
  
  const PM = "PM";
  const EVALUATOR = "USER";
  const ROOT = "root";
   
       
  public function __construct() {

    }
    
    /**
     *  Builds a new User object
     * 
     * @param int $id User ID
     * @param string $name User's name
     * @param string $email User's email
     * @param string $role User's role
     * @param boolean $active Active status
     * @return \self
     */
  public function newUser($id, $name, $email, $role, $active){
    $instance = new self();
    $instance->id = $id;
    $instance->name = $name;
    $instance->email = $email;
    $instance->role = $role;
    $instance->active = $active;  

    return $instance;
  }
  
  /**
   * Checks if the user is admin
   * 
   * @return boolean True if is admin, otherwise false
   */
  function isAdmin() {
    return $this->role == user_dto::ADMIN;
  }
  
  /**
   * Checks if the user is PM
   * 
   * @return boolean True if is PM, otherwise false
   */
  function isPM() {
    return $this->role == user_dto::PM;
  }
  
  /**
   * Check if the user is user (evaluator)
   * 
   * @return boolean True if is evaluator, otherwise false 
   */
  function isUser() {
    return $this->role == user_dto::USER;
  }
}