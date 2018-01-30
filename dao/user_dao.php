<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/db/keopsdb.class.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );
   
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
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
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
        $user->username = $row['username'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUser : " . $ex->getMessage());
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
  
  function getUsers() {
    try {
      $users = array();
      $query = $this->conn->prepare("SELECT id, username, name, email, creation_date, role, active FROM USERS");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user = new user_dto();
        $user->id = $row['id'];
        $user->username = $row['username'];
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
      throw new Exception("Error in user_dao::getUsers : " . $ex->getMessage());
    }
  }

  function getDatatablesUsers($request) {
    try {
      $columns = array(
          array( 'db' => 'id', 'dt' => 0 ),
          array( 'db' => 'username', 'dt' => 1 ),
          array( 'db' => 'name', 'dt' => 2 ),
          array( 'db' => 'email', 'dt' => 3 ),
          array(
              'db'        => 'creation_date',
              'dt'        => 4,
              'formatter' => function( $d, $row ) {
                  return date( 'jS M y', strtotime($d));
              }
          ),
          array( 'db' => 'role', 'dt' => 5 ),
          array( 'db' => 'active', 'dt' => 6)
      );

      return json_encode(
          DatatablesProcessing::simple( $request, $this->conn, "users", "id", $columns )
      );
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesUsers : " . $ex->getMessage());
    }
  }
}
