<?php
/*
 * Methods to work with  sentence_task_dto objects and the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_dto.php");
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
  
  /**
   * Retrieves a parallel sentence object from the DB, given its ID and the ID of its task
   * 
   * @param int $sentence_id Sentence ID
   * @param int $task_id Task ID
   * @return \sentence_task_dto Sentence object
   * @throws Exception
   */
  function getSentenceByIdAndTask($sentence_id, $task_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();
      // TODO We are assuming that sentence ids are consecutive
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, st.evaluation, st.creation_date, st.completed_date from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.id = ? and st.task_id = ? limit 1;");
      $query->bindParam(1, $sentence_id);
      $query->bindParam(2, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = array();
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
      }

      $query = $this->conn->prepare("
        select * from sentences as s
        join sentences_pairing as sp on (s.id = sp.id_2)
        where sp.id_1 = ?;
      ");
      $query->bindParam(1, $sentence_task_dto->sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_dto = new sentence_dto();
        $sentence_dto->id = $row['id'];
        $sentence_dto->corpus_id = $row['corpus_id'];
        $sentence_dto->source_text = $row['source_text'];
        $sentence_dto->type = $row['type'];

        $sentence_task_dto->target_text[] = $sentence_dto;
      }

      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getSentenceById : " . $ex->getMessage());
    }
  }

  /**
   * Retrieves from the DB the first pending sentence for a given task
   * 
   * @param int $task_id Task ID
   * @return \sentence_task_dto Sentence object
   * @throws Exception
   */
  function getNextPendingSentenceByTask($task_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();

      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, st.evaluation, st.creation_date, st.completed_date from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.task_id = ? and st.evaluation = 'P' and s.is_source = true order by st.id asc limit 1;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = array();
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
      }

      $query = $this->conn->prepare("
        select * from sentences as s
        join sentences_pairing as sp on (s.id = sp.id_2)
        where sp.id_1 = ?;
      ");
      $query->bindParam(1, $sentence_task_dto->sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_dto = new sentence_dto();
        $sentence_dto->id = $row['id'];
        $sentence_dto->corpus_id = $row['corpus_id'];
        $sentence_dto->source_text = $row['source_text'];
        $sentence_dto->type = $row['type'];

        $sentence_task_dto->target_text[] = $sentence_dto;
      }

      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getNextPendingSentenceByTask : " . $ex->getMessage());
    }
  }


  /**
   * Retrieves from the DB the first pending sentence for a given task
   * 
   * @param int $task_id Task ID
   * @return \sentence_task_dto Sentence object
   * @throws Exception
   */
  function getFirstSentenceByTask($task_id) {
    try {
      $sentence_task_dto = new sentence_task_dto();
      // TODO We are assuming that sentence ids are consecutive
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, st.evaluation, st.creation_date, st.completed_date from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.task_id = ? order by st.sentence_id ASC limit 1;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = array();
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
      }

      $query = $this->conn->prepare("
        select * from sentences as s
        join sentences_pairing as sp on (s.id = sp.id_2)
        where sp.id_1 = ?;
      ");
      $query->bindParam(1, $sentence_task_dto->sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_dto = new sentence_dto();
        $sentence_dto->id = $row['id'];
        $sentence_dto->corpus_id = $row['corpus_id'];
        $sentence_dto->source_text = $row['source_text'];
        $sentence_dto->type = $row['type'];

        $sentence_task_dto->target_text[] = $sentence_dto;
      }

      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getSentenceById : " . $ex->getMessage());
    }
  }

    /**
     * Searches in the DB a given term in the sentences of a given task
     * 
     * @param int $task_id Task ID
     * @param string $search_term Search term
     * @return int ID of the sentence where the term was found
     * @throws Exception
     */
    function getSentenceIdByTermAndTask($task_id, $search_term) {
    $sentence_id = 0;

    /*
      TODO: When quotes are used in the search term, the query fails.
      This could happen because quotes used in the corpora are not the same
      as the ones input by keyboard.
      This could be fixed by replacing the other types of quotes with the
      ones written with the keyboard: --> " <--
      This replacement should be temporal and can be done directly in the query:
        translate(s.target_text, '“”', '""')

      Another solution would be to add an ILIKE statement that replaces,
      in the search filter, the quotes with an underscore (_). That underscore
      will match any character (and that, besides a solution, can be a problem).
      For example:
        Search term: "compañia"
        Search filter: %_compañía_%

      This affects other functions related to search
    */

    $endinginspace = "%".$search_term." %";
    $endingincomma = "%".$search_term.",%";
    $endingincolon = "%".$search_term.":%";
    $enginginfullstop =  "%".$search_term.";%";
    $enginginsemicolon =  "%".$search_term.".%";
    $startingwithspace = "% ".$search_term."%";
    try {
      $query = $this->conn->prepare("select st.id as sentence_id from sentences_tasks as st left join sentences as s on st.sentence_id = s.id "
              . "where (s.source_text ILIKE ? or s.target_text ILIKE ? "
               . "or s.source_text ILIKE ? or s.target_text ILIKE ? "
               . "or s.source_text ILIKE ? or s.target_text ILIKE ? "
               . "or s.source_text ILIKE ? or s.target_text ILIKE ? "
               . "or s.source_text ILIKE ? or s.target_text ILIKE ? "
               . "or s.source_text ILIKE ? or s.target_text ILIKE ? ) "
              . " and st.task_id = ? limit 1;");
      $query->bindParam(1, $endinginspace);
      $query->bindParam(2, $endinginspace);
      $query->bindParam(3, $endingincomma);
      $query->bindParam(4, $endingincomma);
      $query->bindParam(5, $endingincolon);
      $query->bindParam(6, $endingincolon);
      $query->bindParam(7, $enginginfullstop);
      $query->bindParam(8, $enginginfullstop);
      $query->bindParam(9, $enginginsemicolon);
      $query->bindParam(10, $enginginsemicolon);
      $query->bindParam(11, $startingwithspace);
      $query->bindParam(12, $startingwithspace);
      $query->bindParam(13, $task_id);
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

  /**
   * Retrieves a sentence from the DB, given the ID of the previous one and the task ID
   * 
   * @param int $sentence_id ID of the preceeding sentence
   * @param int $task_id Task ID
   * @return \sentence_task_dto Sentence object
   * @throws Exception
   */
  function gotoSentenceByTask($sentence_id, $task_id) {
    try {
      if ($sentence_id == null || $sentence_id=="") {
        $sentence_id = 1;
      }
      $sentence_task_dto = new sentence_task_dto();
      $offset = $sentence_id - 1;
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, st.evaluation, st.creation_date, st.completed_date from sentences_tasks as st left join sentences as s on st.sentence_id = s.id where st.task_id = ? and s.is_source = true order by st.id asc limit 1 offset ?;");
      $query->bindParam(1, $task_id);
      $query->bindParam(2, $offset);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = array();
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
      }

      $query = $this->conn->prepare("
        select * from sentences as s
        join sentences_pairing as sp on (s.id = sp.id_2)
        where sp.id_1 = ?;
      ");
      $query->bindParam(1, $sentence_task_dto->sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_dto = new sentence_dto();
        $sentence_dto->id = $row['id'];
        $sentence_dto->corpus_id = $row['corpus_id'];
        $sentence_dto->source_text = $row['source_text'];
        $sentence_dto->type = $row['type'];

        $sentence_task_dto->target_text[] = $sentence_dto;
      }

      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::gotoSentenceByTask : " . $ex->getMessage());
    }
  }

  /**
   * Retrieves a sentence from the DB, given the ID of the previous one and the task ID,
   * taking into account applied filters (search term and label)
   * 
   * @param int $sentence_id ID of the preceeding sentence
   * @param int $task_id Task ID
   * @param string $search_term Term to search in source text and target text
   * @param string $label Label to filter the results ("ALL" if not filtering by label)
   * @return \sentence_task_dto Sentence object
   * @throws Exception
   */
  function gotoSentenceByTaskAndFilters($sentence_id, $task_id, $search_term, $label = "ALL") {
    try {
      if ($sentence_id == null || $sentence_id=="") {
        $sentence_id = 1;
      }
  
      $sentence_task_dto = new sentence_task_dto();
      $offset = $sentence_id - 1;
      $query = $this->conn->prepare("select st.id, st.task_id, st.sentence_id, s.source_text, st.evaluation, st.creation_date, st.completed_date from sentences_tasks as st "
        . "left join sentences as s on st.sentence_id = s.id "
        . "left join sentences_pairing as sp on (st.sentence_id = sp.id_1) "
        . "left join sentences as s2 on (sp.id_2 = s2.id) "
        . "where st.task_id = :taskid and s.is_source = true "
        . (($label != "ALL") ? "and st.evaluation = :label " : "")
        . (($search_term != "") ? "and (s.source_text @@ to_tsquery(:searchterm) " : "")
        . (($search_term != "") ? "or s2.source_text @@ to_tsquery(:searchterm2))" : " ")
        . "order by st.id asc limit 1 offset :offset;");
      
      $query->bindParam(':taskid', $task_id);
      $query->bindParam(':offset', $offset);

      if ($search_term != "") $query->bindParam(':searchterm', $search_term);
      if ($search_term != "") $query->bindParam(':searchterm2', $search_term);
      if ($label != "ALL") { $query->bindParam(':label', $label); }

      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto->id = $row['id'];
        $sentence_task_dto->task_id = $row['task_id'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = array();
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->creation_date = $row['creation_date'];
        $sentence_task_dto->completed_date = $row['completed_date'];
      }

      $query = $this->conn->prepare("
        select * from sentences as s
        join sentences_pairing as sp on (s.id = sp.id_2)
        where sp.id_1 = ?;
      ");
      $query->bindParam(1, $sentence_task_dto->sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_dto = new sentence_dto();
        $sentence_dto->id = $row['id'];
        $sentence_dto->corpus_id = $row['corpus_id'];
        $sentence_dto->source_text = $row['source_text'];
        $sentence_dto->type = $row['type'];

        $sentence_task_dto->target_text[] = $sentence_dto;
      }

      $this->conn->close_conn();
      return $sentence_task_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::gotoSentenceByTask : " . $ex->getMessage());
    }
  }

  /**
   * Associates in the DB all sentences from a corpus to a given task
   * 
   * @param int $corpus_id Corpus ID
   * @param int $task_id Task ID
   * @return boolean Returns true if succeeded, otherwise false
   * @throws Exception
   */
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
 
  /**
   * Updates in the DB a given sentence object with its evaluation and completion date
   * 
   * @param object $sentence_task_dto Sentence object to  update
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateSentence($sentence_task_dto) {
    try {
      $query = $this->conn->prepare("UPDATE sentences_tasks SET evaluation = ?, completed_date = ?, time = ? WHERE id = ?;");
      $query->bindParam(1, $sentence_task_dto->evaluation);
      $query->bindParam(2, $sentence_task_dto->completed_date);
      $query->bindParam(3, $sentence_task_dto->time);
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

  /**
   * Retrieves from the DB the completion status of a given task, in comparisson with the current sentence
   * 
   * @param int $sentence_id Current sentence ID
   * @param type $task_id Task ID
   * @return \task_progress_dto Task Progress object
   * @throws Exception
   */
  function getCurrentProgressByIdAndTask($sentence_id, $task_id) {
    try {
      $task_progress_dto = new task_progress_dto();
      $query = $this->conn->prepare("select count(case when st.id <= ? then 1 end) as current, count(*) as total, count(case when st.evaluation<>'P' then 1 end) as completed from sentences_tasks as st, sentences as s where task_id = ? and sentence_id = s.id and s.is_source;");
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

  /**
   * Retrieves from the DB the completion status of a given task, in comparisson with the current sentence
   * and taking into account applied filters.
   * 
   * @param int $sentence_id Current sentence ID
   * @param type $task_id Task ID
   * @param string $search_term Term to search in source text and target text
   * @param string $label Label to filter the results ("ALL" if not filtering by label)
   * @return \task_progress_dto Task Progress object
   * @throws Exception
   */
  function getCurrentProgressByIdAndTaskAndFilters($sentence_id, $task_id, $search_term, $label = "ALL") {
    try {
      $task_progress_dto = new task_progress_dto();
      $query = $this->conn->prepare("
      select count(case when x.id <= :sentenceid then 1 end) as current, 
        count(x.id) as total, count(case when evaluation<>'P' then 1 end) as completed 
        from (
          select st.id, evaluation
          from sentences_tasks as st 
          left join sentences as s on (s.id = st.sentence_id)
          join sentences_pairing as sp on (st.sentence_id = sp.id_1)
          join sentences as s2 on (sp.id_2 = s2.id) "
          . "where task_id = :taskid and s.is_source = true " 
          . (($label != "ALL") ? "and evaluation = :label " : "")
          . (($search_term != "") ? "and (s.source_text @@ to_tsquery(:searchterm) " : "")
          . (($search_term != "") ?" or s2.source_text @@ to_tsquery(:searchterm2))" : "")
          . " group by st.id
        ) as x;"
      );

      $query->bindParam(':sentenceid', $sentence_id);
      $query->bindParam(':taskid', $task_id);

      if ($search_term != "") $query->bindParam(':searchterm', $search_term);
      if ($search_term != "") $query->bindParam(':searchterm2', $search_term);
      if ($label != "ALL") { $query->bindParam(':label', $label); }

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

  /**
   * Retrieves from the DB the stats for a given task
   * 
   * @param int $task_id Task id
   * @return \task_stats_dto Task stats object
   * @throws Exception
   */
  function getStatsByTask($task_id) {
    try {
      $task_stats_dto = new task_stats_dto();
      $query = $this->conn->prepare("select count(*) as total,
count(case when evaluation = 'L' then 1 end) as L,
count(case when evaluation = 'A' then 1 end) as A,
count(case when evaluation = 'T' then 1 end) as T,
count(case when evaluation = 'MT' then 1 end) as MT,
count(case when evaluation = 'E' then 1 end) as E,
count(case when evaluation = 'F' then 1 end) as F,
count(case when evaluation = 'P' then 1 end) as P,
count(case when evaluation = 'V' then 1 end) as V
from sentences_tasks, sentences as s where task_id = ? and sentence_id = s.id and s.is_source = true;");
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

  /**
   * Retrieves from the DB all sentences for a given task, with their evaluation.
   * 
   * @param int $task_id Task ID
   * @return array Array of sentence objects
   * @throws Exception
   */
  function getAnnotatedSentecesByTask($task_id){ 
     try {
      $st_array = array();
      $query = $this->conn->prepare("select st.id as pair, st.sentence_id, s.source_text, st.evaluation from sentences_tasks st left join sentences s  on s.id = st.sentence_id left join tasks t on t.id = st.task_id where st.task_id = ? and s.is_souce = true order by s.id;");
      $query->bindParam(1, $task_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto = new sentence_task_dto();
        $sentence_task_dto->id = $row['pair'];
        $sentence_task_dto->source_text = $row['source_text'];
        $sentence_task_dto->target_text = array();
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->evaluation = $row['evaluation'];
        $sentence_task_dto->sentence_id = $row['sentence_id'];

        $query2 = $this->conn->prepare("
          select s.source_text as target_text from sentences as s
          join sentences_pairing as sp on (s.id = sp.id_2)
          where sp.id_1 = ?;
        ");
        $query2->bindParam(1, $sentence_task_dto->sentence_id);
        $query2->execute();
        $query2->setFetchMode(PDO::FETCH_ASSOC);
        while ($row2 = $query2->fetch()) {
          $sentence_task_dto->target_text[] = $row2['target_text'];
        }

        array_push($st_array, $sentence_task_dto);        
      }
      $this->conn->close_conn();
      return $st_array;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_task_dao::getAnnotatedSentecesByTask: " . $ex->getMessage());
    }
  }
}
