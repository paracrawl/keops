<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_progress_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_stats_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class sentence_task_dao {

  private $conn;

  //public static $columns;

  public function __construct() {
    $this->conn = new keopsdb();
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  function getSentenceByIdAndTask($sentence_id, $task_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();
      // TODO We are assuming that sentence ids are consecutive
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, s.target_text, st.evaluation, st.creation_date, st.completed_date, st.comments from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.id = ? and st.task_id = ? limit 1;");
      $query->bindParam(1, $sentence_id);
      $query->bindParam(2, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
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
      throw new Exception("Error in sentence_task_dao::getSentenceById : " . $ex->getMessage());
    }
  }

  function getNextPendingSentenceByTask($task_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();

      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, s.target_text, st.evaluation, st.creation_date, st.completed_date, st.comments from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.task_id = ? and st.evaluation = 'P'::label order by st.id asc limit 1;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
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

    function getSentenceIdByTermAndTask($task_id, $search_term) {
    $sentence_id = 0;
    $search_term_placeholder = "%".$search_term."%";
    try {
      $query = $this->conn->prepare("select  st.sentence_id as sentence_id from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where (s.source_text ILIKE ? or s.target_text ILIKE ?) and st.task_id = ? limit 1;");
      $query->bindParam(1, $search_term_placeholder);
      $query->bindParam(2, $search_term_placeholder);
      $query->bindParam(3, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);

      while ($row = $query->fetch()) {        
       $sentence_id = $row['sentence_id'];
      }
      $this->conn->close_conn();
      return $sentence_id;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getSentenceById : " . $ex->getMessage());
    }
  }

  function gotoSentenceByTask($sentence_id, $task_id) {
    try {
      if ($sentence_id == null || $sentence_id=="") {
        $sentence_id = 1;
      }
      $sentence_task_dto = new sentence_task_dto();
      $offset = $sentence_id - 1;
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, s.target_text, st.evaluation, st.creation_date, st.completed_date, st.comments from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.task_id = ? order by st.id asc limit 1 offset ?;");
      $query->bindParam(1, $task_id);
      $query->bindParam(2, $offset);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
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
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::insertBatchSentencesFromTask : " . $ex->getMessage());
    }
    return false;
  }


  
  function updateSentence($sentence_task_dto) {
    try {
      $query = $this->conn->prepare("UPDATE sentences_tasks SET evaluation = ?, comments = ?, completed_date = ? WHERE id = ?;");
      $query->bindParam(1, $sentence_task_dto->evaluation);
      $query->bindParam(2, $sentence_task_dto->comments);
      $query->bindParam(3, $sentence_task_dto->completed_date);
      $query->bindParam(4, $sentence_task_dto->id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in project_dao::updateProject : " . $ex->getMessage());
    }
    return false;
  }

  function getCurrentProgressByIdAndTask($sentence_id, $task_id) {
    try {
      $task_progress_dto = new task_progress_dto();
      $query = $this->conn->prepare("select count(case when id <= ? then 1 end) as current, count(*) as total, count(case when evaluation<>'P' then 1 end) as completed from sentences_tasks where task_id = ?;");
      $query->bindParam(1, $sentence_id);
      $query->bindParam(2, $task_id);     
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $task_progress_dto->current = $row['current'];
        $task_progress_dto->total = $row['total'];
        $task_progress_dto->completed = $row['completed'];
      }
      $this->conn->close_conn();
      return $task_progress_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getNextPendingSentenceByTask : " . $ex->getMessage());
    }
  }

  function getStatsByTask($task_id) {
    try {
      $task_stats_dto = new task_stats_dto();
      $query = $this->conn->prepare("select count(*) as total,
count(case when evaluation = 'L'::label then 1 end) as L,
count(case when evaluation = 'A'::label then 1 end) as A,
count(case when evaluation = 'T'::label then 1 end) as T,
count(case when evaluation = 'MT'::label then 1 end) as MT,
count(case when evaluation = 'E'::label then 1 end) as E,
count(case when evaluation = 'F'::label then 1 end) as F,
count(case when evaluation = 'P'::label then 1 end) as P,
count(case when evaluation = 'V'::label then 1 end) as V
from sentences_tasks where task_id = ?;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        foreach (sentence_task_dto::$labels as $label) {
          $task_stats_dto->array_type[$label['value']] = $row[strtolower($label['value'])];
        }
        $task_stats_dto->total = $row['total'];
      }
      $this->conn->close_conn();
      return $task_stats_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getNextPendingSentenceByTask : " . $ex->getMessage());
    }
  }

}
