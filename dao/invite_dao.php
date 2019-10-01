<?php
/**
 * Methods to work with Invite objects in the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/invite_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );


class invite_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  /**
   * Stores a Invite object into the DB
   * 
   * @param object $invite_dto Invite object
   * @return string  "ok" if succeeded, otherwise a string containing the error 
   * @throws Exception
   */
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
        $query = $this->conn->prepare("SELECT * FROM tokens WHERE email = ?;");
        $query->bindParam(1, $invite_dto->email);
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
        $this->conn->close_conn();
        throw new Exception("Error invite_dao::inviteUser : " . $ex->getMessage());
      }
    }
  }

  /**
   * Checks the token and the email, for an invite object, against those stored in the DB
   * 
   * @param object $invite_dto Invite object
   * @return string  "ok" if it succeeded, otherwise a string with the error 
   * @throws Exception
   */
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
      $this->conn->close_conn();
      throw new Exception("Error in invite_dao::checkToken : " . $ex->getMessage());
    }
  }
  
  /**
   * Marks the invitation  as used, by setting its DATE_USED field in DB
   * 
   * @param object $invite_dto Invite object
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function markAsUsed($invite_dto){
    try {
      $query=$this->conn->prepare("UPDATE tokens SET DATE_USED = current_timestamp WHERE email = ?;");
      $query->bindParam(1, $invite_dto->email);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
        $this->conn->close_conn();       
        throw new Exception("Error in invite_dao::markAsUsed : " . $ex->getMessage());
    }
  }
  
  /**
   * Removes an invitation from the DB
   * 
   * @param int $id Invitation ID
   * @return boolean  True if succeeded, otherwise false
   * @throws Exception
   */
  function revokeInvite($id){
    try {
      $query=$this->conn->prepare("DELETE FROM tokens  WHERE id = ?;");
      $query->bindParam(1, $id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
        $this->conn->close_conn();       
        throw new Exception("Error in invite_dao::revokeInvite : " . $ex->getMessage());
    }
  }

  
    /**
     * Retrieves from the DB a list of  invited users, in a Datatables-friendly format
     * 
     * @param request $request GET request
     * @return string JSON enconded list, friendly for Datatables
     * @throws Exception
     */
    function getDatatablesInvited($request, $invited_by) {
    try {
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(self::$columns, 
      "tokens as t left join users u on u.id = t.admin", 
      $request, null,
      ($invited_by != -1) ? "u.id = ?" : null,
      ($invited_by != -1) ? array($invited_by) : null
    ));
    } catch (Exception $ex) {
      throw new Exception("Error in invite_dao::getDatatablesInvited : " . $ex->getMessage());
    }
  }
  
}

/**
 * Datatables columns for the invitations table
 */
invite_dao::$columns = array(
  array('t.id', 'id'),
  array('t.email', 'email'),
  array('t.date_sent', 'date_sent'),
  array('t.date_used', 'date_used'),
  array('t.token', 'token'),
  array('u.name', 'name'),
  array('t.admin', 'admin')
);