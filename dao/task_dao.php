<?php
/**
 * Methods to work with Task objects and the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class task_dao {
  private $conn;
  public static $columns_project_tasks;
  public static $columns_user_tasks;
  public static $columns_corpus_tasks;
  
  public function __construct(){
    $this->conn = new keopsdb();
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  /**
   * Retrieves a task from the DB, given its ID
   * 
   * @param int $id Task ID
   * @return \task_dto Task object
   * @throws Exception
   */
  function getTaskById($id) {
    try {
      $task = new task_dto();
      
      $query = $this->conn->prepare("SELECT * FROM tasks WHERE id = ?;");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $task->id = $row['id'];
        $task->project_id = $row['project_id'];
        $task->assigned_user = $row['assigned_user'];
        $task->corpus_id = $row['corpus_id'];
        $task->size = $row['size'];
        $task->status = $row['status'];
        $task->creation_date = $row['creation_date'];
        $task->assigned_date = $row['assigned_date'];
        $task->completed_date = $row['completed_date'];
      }
      $this->conn->close_conn();
      return $task;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::getTaskById : " . $ex->getMessage());
    }
  }
  
  /**
   * Inserts a new Task into the DB
   * 
   * @param object $task_dto Task object
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function insertTask($task_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO tasks (project_id, assigned_user, corpus_id, assigned_date) VALUES (?, ?, ?, ?);");
      $query->bindParam(1, $task_dto->project_id);
      $query->bindParam(2, $task_dto->assigned_user);
      $query->bindParam(3, $task_dto->corpus_id);      
      $query->bindParam(4, $task_dto->assigned_date);
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
  
  /**
   * Updates the task metadata in the DB with its amount of sentences
   * 
   * @param int $task_id Task  ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateTaskSize($task_id) {
    try {
      $query = $this->conn->prepare("with counted as (select count(task_id) as count from sentences_tasks where task_id = ? group by task_id) update tasks as t set size=s.count from counted as s where t.id = ?;");
      $query->bindParam(1, $task_id);
      $query->bindParam(2, $task_id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::updateTaskSize : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Marks a task in the DB as "Done", including the completion date
   * 
   * @param object $task_dto Task object
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function closeTask($task_dto){
    try {
      $query = $this->conn->prepare("UPDATE TASKS set status='DONE', completed_date = ? where id = ?;");
      $query->bindParam(1, $task_dto->completed_date);
      $query->bindParam(2, $task_dto->id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::closeTask : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Marks a task in the DB as "Started"
   * 
   * @param int $task_id Task ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function startTask($task_id){
    try {
      $query = $this->conn->prepare("UPDATE TASKS set status='STARTED' where id = ?;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::startTask : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Removes a task and the association with its sentences from the DB
   * (the sentences themselves are not removed, only the association)
   * 
   * @param ID $task_id Task ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function removeTask($task_id){
    try{
      //First  remove from sentences_tasks
      $query1 = $this->conn->prepare("delete from sentences_tasks  where task_id = ?");
      $query1->bindParam(1, $task_id);
      $query1->execute();
      //Then remove form tasks
      $query2 = $this->conn->prepare("delete from tasks where id = ?");
      $query2->bindParam(1, $task_id);
      $query2->execute();

      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();   
      throw new Exception("Error in task_dao::removeTask : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Retrieves a task list from the DB, in a Datatables-friendly format
   * 
   * @param object $request GET request
   * @return string JSON with a list of Tasks, in a format ready for Datatables
   * @throws Exception
   */
  function getDatatablesTasks($request) {
    try {
      return json_encode(DatatablesProcessing::complex( $request, $this->conn,
              "tasks as t left join projects as p on p.id = t.project_id left join users as u on u.id = t.assigned_user "
              . "left join sentences_tasks as st on t.id = st.task_id " 
              . "left join corpora as c on c.id = t.corpus_id " ,
              "t.id",
              self::$columns_project_tasks,
              null,
              "project_id=" . $request['p_id'],
              ["t.id", "u.name", "p.id", "u.id", "c.name"]));
    } catch (Exception $ex) {
      throw new Exception("Error in task_dao::getDatatablesTasks : " . $ex->getMessage());
    }
  }
  
    /**
     * Retrieves from the DB a list of tasks that use a given corpus, in a Datatables-friendly format
     * 
     * @param object $request GET request
     * @return string JSON string with a list of the tasks, ready for Datatables
     * @throws Exception
     */
    function getDatatablesTasksByCorpus($request) {
    try {
      return json_encode(DatatablesProcessing::complex( $request, $this->conn,
              "tasks as t left join projects as p on p.id = t.project_id left join users as u on u.id = t.assigned_user "
              . "left join corpora as c on c.id = t.corpus_id ",
              "t.id",
              self::$columns_corpus_tasks,
              null,
              "corpus_id=" . $request['corpus_id'],
              null));
    } catch (Exception $ex) {
      throw new Exception("Error in task_dao::getDatatablesTasksByCorpus : " . $ex->getMessage());
    }
  }
  
  /**
   * Retrieves from the DB the tasks that use a given corpus
   * 
   * @param int $corpus_id Corpus ID
   * @return array Array of task objects
   * @throws Exception
   */
  function getTasksByCorpus($corpus_id){
    try {
      $tasks_array = array();

      $query = $this->conn->prepare("SELECT * FROM tasks WHERE corpus_id = ?;");
      $query->bindParam(1, $corpus_id);
      $query->execute();      
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $task = new task_dto();

        $task->id = $row['id'];
        $task->project_id = $row['project_id'];
        $task->assigned_user = $row['assigned_user'];
        $task->corpus_id = $row['corpus_id'];
        $task->size = $row['size'];
        $task->status = $row['status'];
        $task->creation_date = $row['creation_date'];
        $task->assigned_date = $row['assigned_date'];
        $task->completed_date = $row['completed_date'];
        $task->username = $row['name'];
        $task->email = $row['email'];
        array_push($tasks_array, $task);
      }
      $this->conn->close_conn();
      return $tasks_array;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::getTasksByCorpus : " . $ex->getMessage());
    }
  }
  
  /**
   * Retrieves from the DB a list of tasks from a given project
   * 
   * @param int $project_id Project ID
   * @return array List of Task objects
   * @throws Exception
   */
  function getTasksByProject($project_id){
    try {
      $tasks_array = array();

      $query = $this->conn->prepare("select t.*, u.name, u.email FROM tasks as t left join users as u on u.id = t.assigned_user WHERE project_id = ? order by completed_date desc NULLS last, creation_date DESC;");
      $query->bindParam(1, $project_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $task = new task_dto();

        $task->id = $row['id'];
        $task->project_id = $row['project_id'];
        $task->assigned_user = $row['assigned_user'];
        $task->corpus_id = $row['corpus_id'];
        $task->size = $row['size'];
        $task->status = $row['status'];
        $task->creation_date = $row['creation_date'];
        $task->assigned_date = $row['assigned_date'];
        $task->completed_date = $row['completed_date'];
        $task->username = $row['name'];
        $task->email = $row['email'];
        array_push($tasks_array, $task);
      }
      $this->conn->close_conn();
      return $tasks_array;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::getTaskById : " . $ex->getMessage());
    }
  }

  /**
   * Retrieves from the DB a list of tasks associated to a given user, in a Datatables-friendly format
   * 
   * @param object $request GET request
   * @param int $user_id User ID
   * @return string JSON with a list of tasks, ready for Datatables
   * @throws Exception
   */
  function getDatatablesUserTasks($request, $user_id) {
    try {
      return json_encode(DatatablesProcessing::complex( $request, $this->conn,
              "tasks as t "
              . "left join projects as p on p.id = t.project_id "
              . "left join users as u on u.id = t.assigned_user "
              . "left join langs as l1 on l1.id = p.source_lang "
              . "left join langs as l2 on l2.id = p.target_lang "
              . "left join users as us on us.id = p.owner "
              . "left join sentences_tasks as st on t.id = st.task_id",
              "t.id",
              self::$columns_user_tasks,
              null,
              "t.assigned_user=" . $user_id ,
              ["t.id", "p.name", "l1.langcode", "l2.langcode", "us.email"]));
    } catch (Exception $ex) {
      throw new Exception("Error in task_dao::getDatatablesUserTasks : " . $ex->getMessage());
    }
  }
}

/**
 * Datatables columns for the Project Tasks table
 */
task_dao::$columns_project_tasks = array(
    array( 'db' => 't.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'u.name', 'alias' => 'name', 'dt' => 1 ),
    array( 'db' => 'size', 'dt' => 2 ),
    array( 'db' => 'c.name', 'alias' => 'corpusname', 'dt' => 3 ),
    array( 'db' => 't.status', 'alias' => 'status', 'dt' => 4 ),
    array( 'db' => 't.creation_date', 'alias' => 'creation_date', 'dt' => 5,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 't.assigned_date', 'alias' => 'assigned_date', 'dt' => 6,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 't.completed_date', 'alias' => 'completed_date', 'dt' => 7,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 'p.id', 'alias' => 'p_id', 'dt' => 8 ),
    array( 'db' => 'u.id', 'alias' => 'u_id', 'dt' => 9),
    array( 'db' => "count(case when st.evaluation!='P' then 1 end)", 'alias' => 'completedsentences', 'dt' => 10),
    array( 'db' => 'u.email', 'alias' => 'email', 'dt' => 11),
    array( 'db' => 't.corpus_id', 'alias' => 'corpus_id', 'dt' => 12 )

              
);

/**
* Datatables columns for the User Tasks table 
*/
task_dao::$columns_user_tasks = array(
    array( 'db' => 't.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'p.name', 'alias' => 'name', 'dt' => 1 ),
    array( 'db' => 'l1.langcode', 'alias' => 'source_lang', 'dt' => 2 ),
    array( 'db' => 'l2.langcode', 'alias' => 'target_lang', 'dt' => 3 ),
    array( 'db' => 'size', 'dt' => 4 ),
    array( 'db' => 't.status', 'alias' => 'status', 'dt' => 5 ),
    array( 'db' => 't.creation_date', 'alias' => 'creation_date', 'dt' => 6,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => "count(case when st.evaluation!='P' then 1 end)", 'alias' => 'sentencescompleted', 'dt' => 7),
    array( 'db' => 'us.email', 'alias' => 'email', 'dt' => 8 )
);

/**
 * Datatables columns for the Corpus Tasks table
 */        
task_dao::$columns_corpus_tasks = array(   
    array( 'db' => 't.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'p.name', 'alias' => 'p_name', 'dt' => 1 ),   
    array( 'db' => 'u.name', 'alias' => 'name', 'dt' => 2 ),    
    array( 'db' => 't.size', 'alias' => 'size',  'dt' => 3 ),
    array( 'db' => 'c.name', 'alias' => 'corpusname', 'dt' => 4 ),
    array( 'db' => 't.status', 'alias' => 'status', 'dt' => 5 ),
    array( 'db' => 't.creation_date', 'alias' => 'creation_date', 'dt' => 6,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 't.assigned_date', 'alias' => 'assigned_date', 'dt' => 7,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 't.completed_date', 'alias' => 'completed_date', 'dt' => 8,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } )
//    array( 'db' => 'p.id', 'alias' => 'p_id', 'dt' => 8 ),
//    array( 'db' => 'u.id', 'alias' => 'u_id', 'dt' => 9),
//    array( 'db' => "count(case when st.evaluation!='P' then 1 end)", 'alias' => 'completedsentences', 'dt' => 10),
//    array( 'db' => 'u.email', 'alias' => 'email', 'dt' => 11),
    
  );
        
