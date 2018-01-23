<?php



class user_dto{
  public $id;
  public $username;
  public $name;
  public $email;
  public $creation_date;
  public $role;
  public $password;
  
  public function check_password($pass){
    //md5 or whatever, check and return true/false
  }
  
}