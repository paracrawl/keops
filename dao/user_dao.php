<?php

require_once($_SERVER['DOCUMENT_ROOT'] ."/db/keopsdb.class.php");
require_once($_SERVER['DOCUMENT_ROOT'] ."/dto/user_dto.php");
   
if(!isset($_SESSION)) { 
        session_start(); 
    } 
class user_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }
  function getUser($email) {
    try {
      $user = new user_dto();
      
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE email like ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user->id = $row['id'];
        $user->username = $row['username'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->password = $row['password'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::get_user : " . $ex->getMessage());
    }
  }

  function getUserPassword($email){
    try {
      $query = $this->conn->prepare("SELECT password FROM USERS WHERE email LIKE ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $password = $query->fetch();
      return $password["password"];
      
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUserPassword : " . $ex->getMessage());
    }
  
  }

}

?>