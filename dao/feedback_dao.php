<?php
/**
 * Methods to work with User objects and the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class feedback_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }

  public function insertFeedback($feedback_dto) {
    try {
        $query = $this->conn->prepare("
            INSERT INTO feedback (score, comments, created, user_id, task_id) VALUES (?, ?, NOW(), ?, ?);
        ");
        $query->bindParam(1, $feedback_dto->score);
        $query->bindParam(2, $feedback_dto->comments);
        $query->bindParam(3, $feedback_dto->user_id);
        $query->bindParam(4, $feedback_dto->task_id);
        $query->execute();
        $this->conn->close_conn();
        return true;
      } catch (Exception $ex) {
        $user_dto->id = -1;
        $this->conn->close_conn();
        throw new Exception("Error in user_dao::newUser : " . $ex->getMessage());
      }
  }
}