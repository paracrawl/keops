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

  /**
   * Saves feedback from a user in the database
   * 
   * @param \object $feedback_dto Feedback data
   * @return boolean True if success, error otherwise
   */
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

  /**
   * Checks if a user has sent feedback about a given task
   * 
   * @param int $user_id ID of the user
   * @param int $task_id ID of the task
   * 
   * @return boolean True if the user has sent feedback about the task, false otherwise
   */
  public function hasOpinion($user_id, $task_id) {
    $statistics = array();
    $query = $this->conn->prepare("
      select count(id) as count from feedback as f where f.user_id = ? and f.task_id = ?;
    ");
    $query->bindParam(1, $user_id);
    $query->bindParam(2, $task_id);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);

    $count = 0;
    while ($row = $query->fetch()) {
      $count = $row['count'];
    }
    $this->conn->close_conn();

    return ($count > 0);
  }
}