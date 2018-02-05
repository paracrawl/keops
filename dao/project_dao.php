<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/project_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class project_dao {
  private $conn;
  
  public function __construct(){
    $this->conn = new keopsdb();
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function insertProject($project_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO projects (name, source_lang, target_lang, description, task_size, owner) VALUES (?, ?, ?, ?, ?, ?);");
      $query->bindParam(1, $project_dto->name);
      $query->bindParam(2, $project_dto->source_lang);
      $query->bindParam(3, $project_dto->target_lang);
      $query->bindParam(4, $project_dto->description);
      $query->bindParam(5, $project_dto->task_size);
      $query->bindParam(6, $project_dto->owner);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::insertProject : " . $ex->getMessage());
    }
    return false;
  }
  
  function getDatatablesProjects($request) {
    try {
      $columns = array(
          array( 'db' => 'p.id', 'alias' => 'id', 'dt' => 0 ),
          array( 'db' => 'p.name', 'alias' => 'name', 'dt' => 1 ),
          array( 'db' => 'l1.langcode', 'alias' => 'source_lang', 'dt' => 2 ),
          array( 'db' => 'l2.langcode', 'alias' => 'target_lang', 'dt' => 3 ),
          array( 'db' => 'p.description', 'alias' => 'description', 'dt' => 4 ),
          array( 'db' => 'p.task_size', 'alias' => 'task_size', 'dt' => 5 ),
          array( 'db' => 'p.creation_date', 'alias' => 'creation_date', 'dt' => 6,
              'formatter' => function( $d, $row ) {
                  return date( 'd/m/Y', strtotime($d));
              } ),
          array( 'db' => 'u.username', 'alias' => 'owner', 'dt' => 7 ),
          array( 'db' => 'l1.langname', 'alias' => 'nsource_lang', 'dt' => 8 ),
          array( 'db' => 'l2.langname', 'alias' => 'ntarget_lang', 'dt' => 9 ),
          array( 'db' => 'u.id', 'alias' => 'user_id', 'dt' => 10 )
      );

      return json_encode(DatatablesProcessing::simple( $request, $this->conn,
              "projects as p left join langs as l1 on p.source_lang = l1.id left join langs as l2 on p.target_lang = l2.id left join users as u on p.owner = u.id",
              "p.id",
              $columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesLanguages : " . $ex->getMessage());
    }
  }
}
