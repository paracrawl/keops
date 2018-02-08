<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class user_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }
  function getUser($email) {
    try {
      $user = new user_dto();
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE email = ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->password = $row['password'];
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUser : " . $ex->getMessage());
    }
  }
  
  function getUserById($id) {
    try {
      $user = new user_dto();
      
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE id = ?;");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUserById : " . $ex->getMessage());
    }
  }

  function getUserPassword($email){
    try {
      $query = $this->conn->prepare("SELECT password FROM USERS WHERE email LIKE ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $password = $query->fetch();
      $this->conn->close_conn();
      return $password["password"];
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUserPassword : " . $ex->getMessage());
    }
  }

  function getUsers() {
    try {
      $users = array();
      $query = $this->conn->prepare("SELECT id, name, email, creation_date, role, active FROM USERS");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user = new user_dto();
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->active = $row['active'];
        $users[] = $user;
      }
      $this->conn->close_conn();
      return $users;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUsers : " . $ex->getMessage());
    }
  }

  function newUser($user_dto){
    try {
      $query = $this->conn->prepare("INSERT INTO users (name, email, password, active) VALUES (?, ?, ?, ?);");
      $query->bindParam(1, $user_dto->name);
      $query->bindParam(2, $user_dto->email);
      $query->bindParam(3, $user_dto->password);
      $query->bindValue(4, true);
      $query->execute();
      //$query->setFetchMode(PDO::FETCH_ASSOC);
      $user_dto->id = $this->conn->lastInsertId();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $user_dto->id = -1;
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::newUser : " . $ex->getMessage());
    }
  }

  function updateUser($user_dto) {
    try {
      $query = $this->conn->prepare("UPDATE USERS SET name = ?, email = ?,  role = ?, active = ? WHERE id = ?;");
      $query->bindParam(1, $user_dto->name);
      $query->bindParam(2, $user_dto->email);
      $query->bindParam(3, $user_dto->role);
      $query->bindParam(4, $user_dto->active);
      $query->bindParam(5, $user_dto->id);   
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::updateUser : " . $ex->getMessage());
    }
  }

  function getDatatablesUsers($request) {
    try {

      return json_encode(DatatablesProcessing::simple( $request, $this->conn, "users", "id", self::$columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesUsers : " . $ex->getMessage());
    }
  }
}
user_dao::$columns = array(
    array( 'db' => 'id', 'dt' => 0 ),
    array( 'db' => 'name', 'dt' => 1 ),
    array( 'db' => 'email', 'dt' => 2 ),
    array(
        'db'        => 'creation_date',
        'dt'        => 3,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 'role', 'dt' => 4 ),
    array( 'db' => 'active', 'dt' => 5)
);
