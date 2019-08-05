<?php
/**
 * Methods to work with Project objects and the DB
 */
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
  
  /**
   * Retrieves a project from the DB, given its ID
   * 
   * @param int $id Project ID
   * @return \project_dto Project object
   * @throws Exception
   */
  function getProjectById($id) {
    try {
      $project = new project_dto();
      
      $query = $this->conn->prepare("select p.id, p.name, p.owner, p.description, p.creation_date, p.active, u.name as username, u.email as email from projects as p, users as u where p.owner = u.id and p.id = ?");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $project->id = $row['id'];
        $project->name = $row['name'];
        $project->description = $row['description'];
        $project->creation_date = $row['creation_date'];
        $project->active = $row['active'];
        $project->owner = $row['owner'];
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

  /**
   * Retrieves an array of projects from the DB, given their owner ID
   * 
   * @param int $id Owner ID
   * @return array Project objects
   * @throws Exception
   */
  function getProjectsIdByOwner($id) {
    try {
      $project = new project_dto();
      
      $query = $this->conn->prepare("select id from projects where owner = ? ;");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);

      $ids = array();
      while($row = $query->fetch()){
        $ids[] = $row["id"];
      }

      return $ids;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::getProjectById : " . $ex->getMessage());
    }
  }

  /**
   * Retrieves all the  Projects from the DB
   * 
   * @return array \project_dto  Array of Project objects
   * @throws Exception
   */
  function getProjects() {
    try {
      $projects = array();
      $query = $this->conn->prepare("select p.id, p.name, p.owner, u.name as username from projects as p, users as u, where p.active and p.owner = u.id");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $project = new project_dto();
        $project->id = $row['id'];
        $project->name = $row['name'];
        $projects[] = $project;
      }
      $this->conn->close_conn();
      return $projects;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::getProjects : " . $ex->getMessage());
    }
  }
  
  /**
   * Inserts a new project into the DB
   * 
   * @param object $project_dto Project object
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function insertProject($project_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO projects (name, description, owner) VALUES (?, ?, ?);");
      $query->bindParam(1, $project_dto->name);
      $query->bindParam(2, $project_dto->description);
      $query->bindParam(3, $project_dto->owner);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::insertProject : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Updates in the DB the metadata for a given project
   * 
   * @param object $project_dto Project object, containing the new information
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateProject($project_dto) {
    try {
      $query = $this->conn->prepare("UPDATE PROJECTS SET name = ?, description = ?, active =?  WHERE id = ?;");
      $query->bindParam(1, $project_dto->name);
      $query->bindParam(2, $project_dto->description);
      $query->bindParam(3, $project_dto->active);
      $query->bindParam(4, $project_dto->id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::updateProject : " . $ex->getMessage());
    }
  }

  /**
   * Removes a project from the DB, including its tasks and their sentences
   * 
   * @param int $project_id Project ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function removeProject($project_id){
    try{
      //First  remove from sentences_tasks
      $query1 = $this->conn->prepare("delete from sentences_tasks using tasks where tasks.project_id = ? and sentences_tasks.task_id = tasks.id");
      $query1->bindParam(1, $project_id);
      $query1->execute();
      //Then remove form tasks
      $query2 = $this->conn->prepare("delete from tasks where project_id = ?");
      $query2->bindParam(1, $project_id);
      $query2->execute();
      //Finally, remove project
      $query = $this->conn->prepare("DELETE FROM projects WHERE id = ?;");
      $query->bindParam(1, $project_id);
      $query->execute();
      
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();   
      throw new Exception("Error in project_dao::removeProject : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Retrieves from the DB a list of projects, in a Datatables-friendly format
   * 
   * @param object $request GET request
   * @return string JSON string containing the requested projects for Datatables
   * @throws Exception
   */
  function getDatatablesProjects($request) {
    try {
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(self::$columns,
      "projects as p left join users as u on p.owner = u.id "
      . "left join tasks as t on t.project_id = p.id ",
      $request, "p.id, u.name, u.id",
      "p.owner=?", array(getUserId())));
    } catch (Exception $ex) {
      throw new Exception("Error in project_dao::getDatatablesProjects : " . $ex->getMessage());
    }
  }
}

/**
 * Datatables columns for the Projects table
 */
project_dao::$columns = array(
    array('p.id', 'id'),
    array('p.name', 'name'),
    array('p.description', 'description'),
    array('p.creation_date', 'creation_date'),
    array('u.name', 'owner'),
    array('p.active', 'active'),
    array('u.id', 'user_id'),
    array('count(case when t.project_id > 0 then 1 end)', 'taskcount'),
    array("count(case when t.status = 'DONE' then 1 end)", 'taskdone')            
);
