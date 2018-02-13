<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_progress_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class sentence_task_dao {
  private $conn;
  //public static $columns;
  
  public function __construct(){
    $this->conn = new keopsdb();
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function getSentenceByTask($sentence_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();
      
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, s.target_text, st.evaluation, st.creation_date, st.completed_date, st.comments from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.id = ?;");
      $query->bindParam(1, $sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = $row['target_text'];
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
        $sentence_task_dto->comments = $row['comments'];
      }
      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getSentenceByTask : " . $ex->getMessage());
    }
  }
  
  function getNextPendingSentenceByTask($task_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();
      
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, s.target_text, st.evaluation, st.creation_date, st.completed_date, st.comments from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.task_id = ? and st.evaluation = 'P'::label order by st.id asc limit 1;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = $row['target_text'];
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
        $sentence_task_dto->comments = $row['comments'];
      }
      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getNextPendingSentenceByTask : " . $ex->getMessage());
    }
  }
  
  function insertBatchSentencesFromCorpusToTask($corpus_id, $task_id) {
    try {
      $query = $this->conn->prepare("insert into sentences_tasks (task_id, sentence_id) select ?, id from sentences where corpus_id = ?");
      $query->bindParam(1, $task_id);
      $query->bindParam(2, $corpus_id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      throw new Exception("Error in sentence_task_dao::insertBatchSentencesFromTask : " . $ex->getMessage());
    }
    return false;
  }
  
  function getTaskCurrentProgress($sentence_id, $task_id) {
    try {
      $task_progress_dto = new task_progress_dto();
      error_log($sentence_id);
      $query = $this->conn->prepare("select count(case when id <= ? then 1 end) as current, count(*) as total from sentences_tasks where task_id = ?;");
      $query->bindParam(1, $sentence_id);
      $query->bindParam(2, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $task_progress_dto->current = $row['current'];
        $task_progress_dto->total = $row['total'];
      }
      $this->conn->close_conn();
      return $task_progress_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getNextPendingSentenceByTask : " . $ex->getMessage());
    }
  }
}
