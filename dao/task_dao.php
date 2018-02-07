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
   
  function getDatatablesTasks($request) {
    try {
      return json_encode(DatatablesProcessing::simple( $request, $this->conn,
              "tasks as t left join projects as p on p.id=1 and p.id = t.project_id left join users as u on u.id = t.assigned_user left join (select task_id, count(*) as size from sentences_tasks where task_id = 1 group by id) as st on st.task_id = t.id",
              "t.id",
              self::$columns ));
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

  