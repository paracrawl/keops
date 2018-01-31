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
          array( 'db' => 'id', 'dt' => 0 ),
          array( 'db' => 'name', 'dt' => 1 ),
          array( 'db' => 'source_lang', 'dt' => 2 ),
          array( 'db' => 'target_lang', 'dt' => 3 ),
          array( 'db' => 'description', 'dt' => 4 ),
          array( 'db' => 'task_size', 'dt' => 5 ),
          array( 'db' => 'creation_date', 'dt' => 6 ),
          array( 'db' => 'owner', 'dt' => 7 )
      );

      return json_encode(DatatablesProcessing::simple( $request, $this->conn, "projects", "id", $columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesLanguages : " . $ex->getMessage());
    }
  }
}