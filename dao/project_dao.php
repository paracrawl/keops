<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/project_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class project_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
    $this->conn = new keopsdb();
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function getProjectById($id) {
    try {
      $project = new project_dto();
      
      $query = $this->conn->prepare("select p.id, p.name, p.source_lang, p.target_lang, p.owner, p.description, p.creation_date, p.active, l1.langcode as source_langcode, l2.langcode as target_langcode, l1.langname as source_langname, l2.langname as target_langname, u.name as username, u.email as email from projects as p, users as u, langs as l1, langs as l2 where p.source_lang = l1.id and p.target_lang = l2.id and p.owner = u.id and p.id = ?");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $project->id = $row['id'];
        $project->name = $row['name'];
        $project->source_lang = $row['source_lang'];
        $project->target_lang = $row['target_lang'];
        $project->description = $row['description'];
        $project->creation_date = $row['creation_date'];
        $project->active = $row['active'];
        $project->owner = $row['owner'];
        $project->source_lang_object->id = $row['source_lang'];
        $project->target_lang_object->id = $row['target_lang'];
        $project->source_lang_object->langcode = $row['source_langcode'];
        $project->target_lang_object->langcode = $row['target_langcode'];
        $project->source_lang_object->langname = $row['source_langname'];
        $project->target_lang_object->langname = $row['target_langname'];
        $project->owner_object->id = $row['owner'];
        $project->owner_object->name = $row['username'];
        $project->owner_object->email = $row['email'];
      }
      $this->conn->close_conn();
      return $project;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::getProjectById : " . $ex->getMessage());
    }
  }
  
  function getProjects() {
    try {
      $projects = array();
      $query = $this->conn->prepare("select p.id, p.name, p.source_lang, l1.langcode as source_langcode, p.target_lang, l2.langcode as target_langcode, p.owner, u.name as username from projects as p, users as u, langs as l1, langs as l2 where p.active and p.source_lang = l1.id and p.target_lang = l2.id and p.owner = u.id");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $project = new project_dto();
        $project->id = $row['id'];
        $project->name = $row['name'];
        $project->source_lang = $row['source_lang'];
        $project->target_lang = $row['target_lang'];
        $project->source_lang_object->id = $row['source_lang'];
        $project->target_lang_object->id = $row['target_lang'];
        $project->source_lang_object->langcode = $row['source_langcode'];
        $project->target_lang_object->langcode = $row['target_langcode'];
        $projects[] = $project;
      }
      $this->conn->close_conn();
      return $projects;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::getProjects : " . $ex->getMessage());
    }
  }
  
  function insertProject($project_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO projects (name, source_lang, target_lang, description, owner) VALUES (?, ?, ?, ?, ?);");
      $query->bindParam(1, $project_dto->name);
      $query->bindParam(2, $project_dto->source_lang);
      $query->bindParam(3, $project_dto->target_lang);
      $query->bindParam(4, $project_dto->description);
      $query->bindParam(5, $project_dto->owner);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::insertProject : " . $ex->getMessage());
    }
    return false;
  }
  
  function updateProject($project_dto) {
    try {
      $query = $this->conn->prepare("UPDATE PROJECTS SET name = ?, source_lang = ?, target_lang = ?, description = ?, active =?  WHERE id = ?;");
      $query->bindParam(1, $project_dto->name);
      $query->bindParam(2, $project_dto->source_lang);
      $query->bindParam(3, $project_dto->target_lang);
      $query->bindParam(4, $project_dto->description);
      $query->bindParam(5, $project_dto->active);
      $query->bindParam(6, $project_dto->id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::updateProject : " . $ex->getMessage());
    }
  }

  
  function getDatatablesProjects($request) {
    try {
      return json_encode(DatatablesProcessing::simple( $request, $this->conn,
              "projects as p left join langs as l1 on p.source_lang = l1.id "
              . "left join langs as l2 on p.target_lang = l2.id "
              . "left join users as u on p.owner = u.id "
              . "left join tasks as t on t.project_id = p.id ",
              "p.id",
              self::$columns,
              array("p.id", "l1.langcode", "l1.langname", "l2.langcode", "l2.langname", "u.name",  "u.id")));
    } catch (Exception $ex) {
      throw new Exception("Error in project_dao::getDatatablesProjects : " . $ex->getMessage());
    }
  }
}
project_dao::$columns = array(
    array( 'db' => 'p.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'p.name', 'alias' => 'name', 'dt' => 1 ),
    array( 'db' => 'l1.langcode', 'alias' => 'source_lang', 'dt' => 2 ),
    array( 'db' => 'l2.langcode', 'alias' => 'target_lang', 'dt' => 3 ),
    array( 'db' => 'p.description', 'alias' => 'description', 'dt' => 4 ),
    array( 'db' => 'p.creation_date', 'alias' => 'creation_date', 'dt' => 5,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 'u.name', 'alias' => 'owner', 'dt' => 6 ),
    array( 'db' => 'p.active', 'alias' => 'active', 'dt' => 7 ),
    array( 'db' => 'l1.langname', 'alias' => 'nsource_lang', 'dt' => 8 ),
    array( 'db' => 'l2.langname', 'alias' => 'ntarget_lang', 'dt' => 9 ),
    array( 'db' => 'u.id', 'alias' => 'user_id', 'dt' => 10 ),
    array( 'db' => 'count(case when t.project_id > 0 then 1 end)', 'alias' => 'taskcount', 'dt' => 11 ),
    array( 'db' => "count(case when t.status = 'DONE' then 1 end)", 'alias' => 'taskdone', 'dt' => 12)            
);
