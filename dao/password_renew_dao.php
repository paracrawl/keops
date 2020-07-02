<?php
/**
 * Methods to work with User objects and the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/password_renew_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class password_renew_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }

  public function getRenewTokenByUser($user_id) {
    try {
        $query = $this->conn->prepare("
            SELECT token, user_id, created_time FROM password_renew WHERE user_id = ?
        ");
        $query->bindParam(1, $user_id);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);

        while($row = $query->fetch()){
            $password_renew_dto = new password_renew_dto();
            $password_renew_dto->token = $row['token'];
            $password_renew_dto->user_id = $row['user_id'];
            $password_renew_dto->created_time = $row['created_time'];
        }

        $this->conn->close_conn();
        return $password_renew_dto;
    } catch (Exception $ex) {
        $this->conn->close_conn();
        throw new Exception("Error in password_renew_dao::getRenewToken : " . $ex->getMessage());
    }
  }

  public function getRenewToken($token) {
    try {
        $query = $this->conn->prepare("
            SELECT token, user_id, created_time FROM password_renew WHERE token LIKE ?
        ");
        $query->bindParam(1, $token);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);

        while($row = $query->fetch()){
            $password_renew_dto = new password_renew_dto();
            $password_renew_dto->token = $row['token'];
            $password_renew_dto->user_id = $row['user_id'];
            $password_renew_dto->created_time = $row['created_time'];
        }

        $this->conn->close_conn();
        return $password_renew_dto;
    } catch (Exception $ex) {
        $this->conn->close_conn();
        throw new Exception("Error in password_renew_dao::getRenewToken : " . $ex->getMessage());
    }
  }

  public function generateRenewToken($user_id) {
    $token = password_hash(random_str(), PASSWORD_DEFAULT);

    try {
        $query = $this->conn->prepare("
            INSERT INTO password_renew (token, user_id) VALUES (?, ?)
            ON CONFLICT ON CONSTRAINT password_renew_user_key 
            DO UPDATE SET token = ?
            RETURNING token, user_id, created_time;
        ");

        $query->bindParam(1, $token);
        $query->bindParam(2, $user_id);
        $query->bindParam(3, $token);
        $query->execute();

        while($row = $query->fetch()){
            $password_renew_dto = new password_renew_dto();
            $password_renew_dto->token = $row['token'];
            $password_renew_dto->user_id = $row['user_id'];
            $password_renew_dto->created_time = $row['created_time'];
        }

        $this->conn->close_conn();
        return $password_renew_dto;
      } catch (Exception $ex) {
        $this->conn->close_conn();
        throw new Exception("Error in password_renew_dao::generateRenewToken : " . $ex->getMessage());
      }  
  }

  public function revokeTokenByUserId($user_id) {
    try {
        $query = $this->conn->prepare("
            delete from password_renew WHERE user_id = ?
        ");
        $query->bindParam(1, $user_id);
        $query->execute();

        $this->conn->close_conn();
        return true;
    } catch (Exception $ex) {
        $this->conn->close_conn();
        throw new Exception("Error in password_renew_dao::revokeTokenByUserId : " . $ex->getMessage());
    }
  }


  public function revokeToken($token) {
    try {
        $query = $this->conn->prepare("
            delete from password_renew WHERE token = ?
        ");
        $query->bindParam(1, $token);
        $query->execute();

        $this->conn->close_conn();
        return true;
    } catch (Exception $ex) {
        $this->conn->close_conn();
        throw new Exception("Error in password_renew_dao::revokeToken : " . $ex->getMessage());
    }
  }
}