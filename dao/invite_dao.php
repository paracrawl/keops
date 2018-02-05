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
  
  function inviteUser($invite_dto) {
    try {
      // $invite = new invite_dto();
      $query = $this->conn->prepare("INSERT INTO tokens (admin, token, email) VALUES (?, ?, ?);");
      $query->bindParam(1, $invite_dto->admin);
      $query->bindParam(2, $invite_dto->token);
      $query->bindParam(3, $invite_dto->email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $this->conn->close_conn();
      return "ok";
    } catch (Exception $ex) {
      //throw new Exception("Error in user_dao::inviteUser : " . $ex->getMessage());
      try {
        $query = $this->conn->prepare("SELECT * FROM tokens;");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($row = $query->fetch()) {
          $invite_dto->id = $row['id'];
          $invite_dto->admin = $row['admin'];
          $invite_dto->token = $row['token'];
          $invite_dto->email = $row['email'];
          $invite_dto->date_sent = $row['date_sent'];
          $invite_dto->date_used = $row['date_used'];
        }
        $this->conn->close_conn();
        return "alreadyexisted";
      } catch (Exception $ex) {
        return "error";
      }
    }
  }

  
  function checkToken($invite_dto) {
    try {
      $query = $this->conn->prepare("SELECT * FROM tokens WHERE email = ?;");
      $query->bindParam(1, $invite_dto->email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $this->conn->close_conn();
      $rows = $query->fetchAll();
      if (count($rows) == 0) {
        return "emailnotfound";
      }
      if (count($rows) == 1) {
        if ($rows[0]["token"] == $invite_dto->token) {
          return "ok";
        } else {
          return "tokennotmatching";
        }
      }
      return "error";
    } catch (Exception $ex) {
      return error;
    }
  }
  
  function markAsUsed($invite_dto){
    try {
      $query=$this->conn->prepare("UPDATE tokens SET DATE_USED = current_timestamp;");
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
        $this->conn->close_conn();
        return false;
    }
  }

}
