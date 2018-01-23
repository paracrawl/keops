<?php

require_once("db/keopsdb.class.php");
require_once("dto/user_dto.php");
   
class user_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }
  
  function get_user($username) {
    try {
      $user = new user_dto();
      
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE username like ?;");
      $query->bindParam(1, $username);
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

  function get_users() {

  }

}

?>