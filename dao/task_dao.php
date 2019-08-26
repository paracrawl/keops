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

      $query = $this->conn->prepare("
        SELECT t.*, l1.id as source_langid, l1.langname as source_langname, l2.id as target_langid, l2.langname as target_langname 
        FROM tasks as t
        left join users as u on u.id = t.assigned_user
        left join langs as l1 on l1.langcode = t.source_lang
        left join langs as l2 on l2.langcode = t.target_lang
        where t.id = ?;
      ");
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
        $task->source_lang = $row['source_lang'];
        $task->target_lang = $row['target_lang'];
        $task->mode = $row['mode'];

        $task->source_lang_object = new stdClass();
        $task->target_lang_object = new stdClass();
        $task->source_lang_object->id = $row['source_langid'];
        $task->target_lang_object->id = $row['target_langid'];
        $task->source_lang_object->langcode = $row['source_lang'];
        $task->target_lang_object->langcode = $row['target_lang'];
        $task->source_lang_object->langname = $row['source_langname'];
        $task->target_lang_object->langname = $row['target_langname'];
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
      $target_lang = ($task_dto->target_lang == "-1") ? NULL : $task_dto->target_lang;
      $query = $this->conn->prepare("INSERT INTO tasks (project_id, assigned_user, corpus_id, assigned_date, source_lang, target_lang, mode) VALUES (?, ?, ?, ?, ?, ?, ?::mode);");
      $query->bindParam(1, $task_dto->project_id);
      $query->bindParam(2, $task_dto->assigned_user);
      $query->bindParam(3, $task_dto->corpus_id);      
      $query->bindParam(4, $task_dto->assigned_date);
      $query->bindParam(5, $task_dto->source_lang);
      $query->bindParam(6, $target_lang);
      $query->bindParam(7, $task_dto->mode);

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
      // First we remove the comments
      $query1 = $this->conn->prepare("delete from comments as c using sentences_tasks as st where c.pair = st.id and st.task_id = ?;");
      $query1->bindParam(1, $task_id);
      $query1->execute();

      //First  remove from sentences_tasks
      $query2 = $this->conn->prepare("delete from sentences_tasks  where task_id = ?");
      $query2->bindParam(1, $task_id);
      $query2->execute();

      //Then remove form tasks
      $query3 = $this->conn->prepare("delete from tasks where id = ?");
      $query3->bindParam(1, $task_id);
      $query3->execute();

      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();   
      throw new Exception("Error in task_dao::removeTask : " . $ex->getMessage());
    }
    return false;
  }

  /**
   * Updates the assigned user for a task
   * 
   * @param int $task_id Task  ID
   * @param int $assigned_user User ID to be assigned to the task
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateAssignedUser($task_id, $assigned_user) {
    try {
      $query = $this->conn->prepare("update tasks set assigned_user = ? where id = ?;");
      $query->bindParam(1, $assigned_user);
      $query->bindParam(2, $task_id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::updateAssignedUser : " . $ex->getMessage());
    }
    return false;
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

      $query = $this->conn->prepare("
        SELECT t.*, l1.id as source_langid, l1.langname as source_langname, l2.id as target_langid, l2.langname as target_langname
        FROM tasks as t 
        left join langs as l1 on (l1.langcode = t.source_lang)
        left join langs as l2 on (l2.langcode = t.target_lang)
        where t.corpus_id = ?;
      ");

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
        $task->source_lang = $row['source_lang'];
        $task->target_lang = $row['target_lang'];
        $task->mode = $row['mode'];

        $task->source_lang_object = new stdClass();
        $task->target_lang_object = new stdClass();
        $task->source_lang_object->id = $row['source_lang'];
        $task->target_lang_object->id = $row['target_lang'];
        $task->source_lang_object->langcode = $row['source_langcode'];
        $task->target_lang_object->langcode = $row['target_langcode'];
        $task->source_lang_object->langname = $row['source_langname'];
        $task->target_lang_object->langname = $row['target_langname'];

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

      $query = $this->conn->prepare("
        select t.*, u.name, u.email, l1.id as source_langid, l1.langname as source_langname, l2.id as target_langid, l2.langname as target_langname 
        FROM tasks as t 
        left join users as u on u.id = t.assigned_user
        left join langs as l1 on l1.langcode = t.source_lang
        left join langs as l2 on l2.langcode = t.target_lang
        WHERE project_id = ?
        order by completed_date desc NULLS last, creation_date DESC;
      ");
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
        $task->source_lang = $row['source_lang'];
        $task->target_lang = $row['target_lang'];
        $task->mode = $row['mode'];

        $task->source_lang_object = new stdClass();
        $task->target_lang_object = new stdClass();
        $task->source_lang_object->id = $row['source_langid'];
        $task->target_lang_object->id = $row['target_langid'];
        $task->source_lang_object->langcode = $row['source_lang'];
        $task->target_lang_object->langcode = $row['target_lang'];
        $task->source_lang_object->langname = $row['source_langname'];
        $task->target_lang_object->langname = $row['target_langname'];
        
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
   * Retrieves from the DB a list of tasks from a given assigned user
   * 
   * @param int $project_id Project ID
   * @return array List of Task objects
   * @throws Exception
   */
  function getTasksByAsignedUser($user_id){
    try {
      $tasks_array = array();

      $query = $this->conn->prepare("
        select t.*, u.name, u.email, l1.id as source_langid, l1.langname as source_langname, l2.id as target_langid, l2.langname as target_langname 
        FROM tasks as t 
        left join users as u on u.id = t.assigned_user
        left join langs as l1 on l1.langcode = t.source_lang
        left join langs as l2 on l2.langcode = t.target_lang
        WHERE assigned_user = ?
        order by completed_date desc NULLS last, creation_date DESC;
      ");
      $query->bindParam(1, $user_id);
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
        $task->source_lang = $row['source_lang'];
        $task->target_lang = $row['target_lang'];
        $task->mode = $row['mode'];

        $task->source_lang_object = new stdClass();
        $task->target_lang_object = new stdClass();
        $task->source_lang_object->id = $row['source_langid'];
        $task->target_lang_object->id = $row['target_langid'];
        $task->source_lang_object->langcode = $row['source_lang'];
        $task->target_lang_object->langcode = $row['target_lang'];
        $task->source_lang_object->langname = $row['source_langname'];
        $task->target_lang_object->langname = $row['target_langname'];
        
        array_push($tasks_array, $task);
      }
      $this->conn->close_conn();
      return $tasks_array;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::getTasksByAsignedUser : " . $ex->getMessage());
    }
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
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(self::$columns_project_tasks,
              "tasks as t left join projects as p on p.id = t.project_id left join users as u on u.id = t.assigned_user "
              . "left join sentences_tasks as st on t.id = st.task_id " 
              . "left join corpora as c on c.id = t.corpus_id ",
              $request,
              "t.id, u.name, p.id, u.id, c.name",
              "project_id= ? ",
              array($request['p_id'])));
    } catch (Exception $ex) {
      throw new Exception("Error in task_dao::getDatatablesTasks : " . $ex->getMessage());
    }
  }


  /**
   * Retrieves statistics for adequacy task. The returned array provides the
   * evaluation score and the amount of sentences with that score.
   * 
   * @param int $sentence_id Current sentence ID
   * @param type $task_id Task ID
   * @return array Statistics
   * @throws Exception
   */
  function getStatsForTask($task_id) {
    try {
      $statistics = array();
      $query = $this->conn->prepare("
        select round(evaluation::integer, -1) as evaluation, count(evaluation) as count
        from sentences_tasks where task_id = ? and evaluation != 'P'
        group by 1;
      ");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);

      while ($row = $query->fetch()) {
        $statistics[$row['evaluation']] = $row["count"];
      }

      $this->conn->close_conn();
      return $statistics;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::getStatsForTask : " . $ex->getMessage());
    }
  }


  /**
   * Retrieves statistics for every task that uses the same corpus as
   * the task given by parameter. The returned array provides the
   * evaluation score and the amount of sentences with that score.
   * 
   * @param int $sentence_id Current sentence ID
   * @param type $task_id Task ID
   * @return array Statistics
   * @throws Exception
   */
  function getInterStatsForTask($task_id, $mode = "ADE") {
    try {
      $statistics = array();
      $query = $this->conn->prepare("
        select t.id, st.evaluation, count(*) from tasks as t
        join sentences_tasks as st on t.id = st.task_id
        where t.mode = ? and t.corpus_id = (select corpus_id from tasks where id = ?)
        group by 1,2;
      ");
      $query->bindParam(1, $mode);
      $query->bindParam(2, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);

      while ($row = $query->fetch()) {
        if($statistics[$row['id']]){
          $statistics[$row['id']][$row['evaluation']] = $row['count'];
        } else {
          $statistics[$row['id']] = array($row['evaluation'] => $row['count']);
        }
      }

      $this->conn->close_conn();
      return $statistics;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in task_dao::getStatsForTask : " . $ex->getMessage());
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
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(self::$columns_corpus_tasks,
              "tasks as t left join projects as p on p.id = t.project_id left join users as u on u.id = t.assigned_user " . "left join corpora as c on c.id = t.corpus_id ",
              $request,
              "",
              "corpus_id=?",
              array($request['corpus_id'])));
    } catch (Exception $ex) {
      throw new Exception("Error in task_dao::getDatatablesTasksByCorpus : " . $ex->getMessage());
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
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(
              self::$columns_user_tasks,
              "tasks as t "
              . "left join projects as p on p.id = t.project_id "
              . "left join users as u on u.id = t.assigned_user "
              . "left join users as us on us.id = p.owner "
              . "left join sentences_tasks as st on t.id = st.task_id",
              $request,
              "t.id, p.name, source_lang, target_lang, us.email",
              "t.assigned_user= ? ",
              array($user_id)));
    } catch (Exception $ex) {
      throw new Exception("Error in task_dao::getDatatablesUserTasks : " . $ex->getMessage());
    }
  }
}

/**
 * Datatables columns for the Project Tasks table
 */
task_dao::$columns_project_tasks = array(
    array('t.id', 'id'),
    array('u.name', 'name'),
    array('t.source_lang', 'source_lang'),
    array('t.target_lang', 'target_lang'),
    array('size'),
    array('c.name', 'corpusname'),
    array('t.status', 'status'),
    array('t.creation_date', 'creation_date'),
    array('t.assigned_date', 'assigned_date'),
    array('t.completed_date', 'completed_date'),
    array('p.id', 'p_id'),
    array('u.id', 'u_id'),
    array("count(case when st.evaluation!='P' then 1 end)", 'completedsentences'),
    array('u.email', 'email'),
    array('t.corpus_id', 'corpus_id')
);

/**
* Datatables columns for the User Tasks table 
*/
task_dao::$columns_user_tasks = array(
    array('t.id', 'id'),
    array('p.name', 'name'),
    array('t.source_lang', 'source_lang'),
    array('t.target_lang', 'target_lang'),
    array('size'),
    array('t.status', 'status'),
    array('t.creation_date', 'creation_date'),
    array("count(case when st.evaluation!='P' then 1 end)", 'sentencescompleted'),
    array('us.email', 'email'),
    array('t.mode', 'mode')
);

/**
 * Datatables columns for the Corpus Tasks table
 */        
task_dao::$columns_corpus_tasks = array(   
    array('t.id', 'id'),
    array('p.name', 'p_name'),   
    array('u.name', 'name'),    
    array('t.size', 'size',),
    array('c.name', 'corpusname'),
    array('t.status', 'status'),
    array('t.creation_date', 'creation_date'),
    array('t.assigned_date', 'assigned_date'),
    array('t.completed_date', 'completed_date')
);
