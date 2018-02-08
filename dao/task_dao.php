<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class task_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
    $this->conn = new keopsdb();
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function insertTask($task_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO tasks (project_id, assigned_user, corpus_id) VALUES (?, ?, ?);");
      $query->bindParam(1, $task_dto->project_id);
      $query->bindParam(2, $task_dto->assigned_user);
      $query->bindParam(3, $task_dto->corpus_id);
      $query->execute();
      $task_dto->id = $this->conn->lastInsertId();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::insertTask : " . $ex->getMessage());
    }
    return false;
  }
  
  function updateTaskSize($task_id) {
    try {
      $query = $this->conn->prepare("with counted as (select count(task_id) as count from sentences_tasks where task_id = ? group by task_id) update tasks as t set size=s.count from counted as s where t.id = ?;");
      $query->bindParam(1, $task_id);
      $query->bindParam(2, $task_id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      throw new Exception("Error in sentence_dao::updateTaskSize : " . $ex->getMessage());
    }
    return false;
  }
  
  function getDatatablesTasks($request) {
    try {
      return json_encode(DatatablesProcessing::complex( $request, $this->conn,
              "tasks as t left join projects as p on p.id=" . $request['p_id'] . " and p.id = t.project_id left join users as u on u.id = t.assigned_user",
              "t.id",
              self::$columns,
              null,
              "project_id=" . $request['p_id'] ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesTasks : " . $ex->getMessage());
    }
  }
}
task_dao::$columns = array(
    array( 'db' => 't.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'u.name', 'alias' => 'name', 'dt' => 1 ),
    array( 'db' => 'size', 'dt' => 2 ),
    array( 'db' => 't.status', 'alias' => 'status', 'dt' => 3 ),
    array( 'db' => 't.creation_date', 'alias' => 'creation_date', 'dt' => 4,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 't.assigned_date', 'alias' => 'assigned_date', 'dt' => 5,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 't.completed_date', 'alias' => 'completed_date', 'dt' => 6,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 'p.id', 'alias' => 'p_id', 'dt' => 7 ),
    array( 'db' => 'u.id', 'alias' => 'u_id', 'dt' => 8 )
);

  