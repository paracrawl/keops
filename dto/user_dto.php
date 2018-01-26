<?php

class user_dto{
  public $id;
  public $username;
  public $name;
  public $email;
  public $creation_date;
  public $role;
  public $password;
  
  const ADMIN = "ADMIN";
  const STAFF = "STAFF";
  const EVALUATOR = "USER";

  function isAdmin() {
    return $this->role == user_dto::ADMIN;
  }
  
  function isStaff() {
    return $this->role == user_dto::STAFF;
  }
}