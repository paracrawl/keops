<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/invite_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class invite_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function inviteUser($admin, $token, $email) {
    try {
     // $invite = new invite_dto();

      $query = $this->conn->prepare("INSERT INTO tokens (admin, token, email) VALUES (?, ?, ?);");
      $query->bindParam(1, $admin);
      $query->bindParam(2, $token);
      $query->bindParam(3, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
//      while ($row = $query->fetch()) {
//        $user->id = $row['id'];
//        $user->username = $row['username'];
//        $user->name = $row['name'];
//        $user->email = $row['email'];
//        $user->creation_date = $row['creation_date'];
//        $user->role = $row['role'];
//        $user->password = $row['password'];
//        $user->active = $row['active'];
//      }
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::inviteUser : " . $ex->getMessage());
    }
  }
  
}