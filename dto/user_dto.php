<?php

class user_dto{
  public $id;
  public $name;
  public $email;
  public $creation_date;
  public $role;
  public $password;
  public $active;
  
  const ADMIN = "ADMIN";
  const STAFF = "STAFF";
  const EVALUATOR = "USER";
   
       
  public function newUser($id, $name, $email, $role, $active){
    $instance = new self();
    $instance->id = $id;
    $instance->name = $name;
    $instance->email = $email;
    $instance->role = $role;
    $instance->active = $active;  
    return $instance;
  }
  
  
  function isAdmin() {
    return $this->role == user_dto::ADMIN;
  }
  
  function isStaff() {
    return $this->role == user_dto::STAFF;
  }
  
  function isUser() {
    return $this->role == user_dto::USER;
  }
}