<?php
/**
 * Methods to work with User objects and the DB
 */
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
  /**
   * Retrieves from the DB all the information for a given user, given its email address
   * 
   * @param string $email User's email address
   * @return \user_dto User object
   * @throws Exception
   */
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

  /**
   * Retrieves from the DB the ID of the first admin
   * 
   * @return int ID of the first admin
   * @throws Exception
   */
  function getFirstAdminId() {
    try {
      $id = -1;
      $query = $this->conn->prepare("SELECT id FROM USERS WHERE role = 'ADMIN' and active = true order by id asc limit 1");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $id = $row['id'];
      }
      $this->conn->close_conn();
      return $id;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUser : " . $ex->getMessage());
    }
  }

 
  /**
   * Retrieves from the DB all the information for a given user, given its ID
   * 
   * @param int $id User ID
   * @return \user_dto User object
   * @throws Exception
   */
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

    /**
   * Retrieves from the DB all the information for a given user, given its email
   * 
   * @param int $email Email address
   * @return \user_dto User object
   * @throws Exception
   */
  function getUserByEmail($email) {
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
        $user->active = $row['active'];
      }
      $this->conn->close_conn();
      return $user;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUserById : " . $ex->getMessage());
    }
  }

  
  /**
   * Retrieves from the DB a list of users, given their IDs
   * 
   * @param array $ids List of User IDs
   * @return array Array of User objects
   * @throws Exception
   */
  function getUsersByIds($ids) {

    try {
      $users = array();
      $inQuery = implode(',', array_fill(0, count($ids), '?'));
      
      $query = $this->conn->prepare("SELECT * FROM USERS WHERE id IN(". $inQuery. ") AND active=true;");
      foreach($ids as $key => $id){
        $query->bindValue(($key+1), $id);
      }
      
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $user = new user_dto();
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->creation_date = $row['creation_date'];
        $user->role = $row['role'];
        $user->active = $row['active'];
        array_push($users, $user);
      }
      $this->conn->close_conn();
      return $users;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUserByIds : " . $ex->getMessage());
    }
  }

  /**
   * Retrievesfrom the DB the (encoded) password for a user, given its email address
   * 
   * @param string $email Email address
   * @return string Encoded password
   * @throws Exception
   */
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

  /**
   * Retrieves all the users from the DB
   * 
   * @return array \user_dto Array of User objects
   * @throws Exception
   */
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

  /**
   * Inserts a new User into the DB
   * 
   * @param object $user_dto User object
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
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
      $user_dto->active= true;
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $user_dto->id = -1;
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::newUser : " . $ex->getMessage());
    }
  }

  /**
   * Updates in the DB the information of a given user
   * 
   * @param object $user_dto User object, containing the new information
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateUser($user_dto) {
    try {
      if (strtolower($user_dto->role) == "root") throw new Exception();

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

    /**
   * Updates in the DB the password of a given user
   * 
   * @param string $password The password of the user
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateUserPassword($id, $password) {
    try {
      $query = $this->conn->prepare("UPDATE USERS SET password = ? WHERE id = ?;");
      $query->bindParam(1, $password);
      $query->bindParam(2, $id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::updateUser : " . $ex->getMessage());
    }
  }

  /**
   * Retrieves from the DB a list of users, in a Datatables-friendly format
   * 
   * @param object $request GET request
   * @return string JSON string containing the list of users, ready for Datatables
   * @throws Exception
   */
  function getDatatablesUsers($request, $invited_by) {
    try {
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(
        self::$columns, 
        "users as u left join tokens as t on (u.email = t.email)", 
        $request, null,
        ($invited_by != -1) ? "t.admin = ? and u.role != 'root'" : "u.role != 'root'",
        ($invited_by != -1) ? array($invited_by) : null
      ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesUsers : " . $ex->getMessage());
    }
  }
}

/**
 * Datatables columns for the  Users table 
 */
user_dao::$columns = array(
    array('u.id'),
    array('u.name'),
    array('u.email'),
    array('u.creation_date'),
    array('u.role'),
    array('u.active')
);
