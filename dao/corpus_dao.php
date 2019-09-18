<?php
/**
 * Methods to work with Corpus objects in the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/corpus_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class corpus_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  /**
   * Retrieves from the DB all corpora metadata (not the sentences)
   * 
   * @return array Array containing corpus objects
   * @throws Exception
   */
  function getCorpora() {
    try {
      $corpora = array();
      $query = $this->conn->prepare("SELECT id, name, source_lang, target_lang FROM corpora");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $corpus = new corpus_dto();
        $corpus->id = $row['id'];
        $corpus->name = $row['name'];
        $corpus->source_lang = $row['source_lang'];
        $corpus->target_lang = $row['target_lang'];
        $corpora[] = $corpus;
      }
      $this->conn->close_conn();
      return $corpora;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in corpus_dao::getCorpora : " . $ex->getMessage());
    }
  }
  
  /**
   * Retrieves from the DB the metadata (not the sentences) for a given corpus
   * 
   * @param int $id Corpus id
   * @return \corpus_dto Corpus object
   * @throws Exception
   */
  function getCorpusById($id){
      try {
      $corpus = new corpus_dto();
      
      $query = $this->conn->prepare("select * from corpora where id=?;");
      $query->bindParam(1, $id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $corpus->id = $row["id"];
        $corpus->name = $row["name"];
        $corpus->source_lang = $row["source_lang"];
        $corpus->target_lang = $row["target_lang"];
        $corpus->lines = $row["lines"];
        $corpus->creation_date = $row["creation_date"];
        $corpus->active = $row["active"];
        $corpus->mode = $row["mode"];
      }
      $this->conn->close_conn();
      return $corpus;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in corpus_dao::getCorpusById : " . $ex->getMessage());
    }
  }

  /**
   * Retrieves from the DB a array of corpora, filtered by the criteria provided by parameter
   * 
   * @param array $filters Map with keys (name of rows) and values to filter.
   * @return array Array of corpora matching the filters
   * @throws Exception
   */
  function getFilteredCorpora($filters) {
    try {
      $sql = "SELECT * FROM corpora";
      if (count($filters) > 0) {
        $where = array();
        foreach ($filters as $key => $value) {
          $where[] = $key . "=" . $value . " ";
        }
        $sql .= " where " . implode(" AND ", $where);
        $sql .= " order by creation_date desc";
      }
      
      $corpora = array();
      $query = $this->conn->prepare($sql);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $corpus = new corpus_dto();
        $corpus->id = $row["id"];
        $corpus->name = $row["name"];
        $corpus->source_lang = $row["source_lang"];
        $corpus->target_lang = $row["target_lang"];
        $corpus->lines = $row["lines"];
        $corpus->creation_date = $row["creation_date"];
        $corpus->active = $row["active"];
        $corpus->mode = $row["mode"];
        $corpora[] = $corpus;
      }
      $this->conn->close_conn();
      return $corpora;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in corpus_dao::getCorpora : " . $ex->getMessage());
    }
  }
          
  /**
   * Retrieves from the DB a list of corpora, in a Datatables-friendly format
   * 
   * @param type $request GET request
   * @return string A JSON for Datatables, containing the list of corpora
   * @throws Exception
   */
  function getDatatablesCorpora($request) {
    try {
      $dtProc = new DatatablesProcessing($this->conn);
      return json_encode($dtProc->process(self::$columns,
              "corpora as c left join langs as l1 on c.source_lang = l1.id left join langs as l2 on c.target_lang = l2.id",
              $request));
    } catch (Exception $ex) {
      throw new Exception("Error in corpus_dao::getDatatablesCorpora : " . $ex->getMessage());
    }
  }
  
  /**
   * Inserts in the DB the corpus metadata (not the sentences) of a corpus
   * 
   * @param object $corpus_dto Corpus object to be stored
   * @return True if succeeded, otherwise false
   * @throws Exception
   */
  function insertCorpus($corpus_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO corpora (name, source_lang, target_lang, mode) VALUES (?, ?, ?, ?::mode);");
      $query->bindParam(1, $corpus_dto->name);
      $query->bindValue(2, ($corpus_dto->source_lang != "NULL" ? $corpus_dto->source_lang : NULL));
      $query->bindParam(3, $corpus_dto->target_lang);
      $query->bindParam(4, $corpus_dto->mode);
      $query->execute();
      $corpus_dto->id = $this->conn->lastInsertId();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $corpus_dto->id = -1;
      $this->conn->close_conn();   
      throw new Exception("Error in corpus_dao::insertCorpus : " . $ex->getMessage());
    }
    return false;
  }
  

  /**
   * Removes from the DB the metadata of a corpus, when the upload fails
   * 
   * @param int $corpus_id Corpus ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function deleteCorpus($corpus_id){
    //Used ONLY when upload fails
    try {
      $query = $this->conn->prepare("DELETE FROM corpora WHERE id = ?;");
      $query->bindParam(1, $corpus_id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();   
      throw new Exception("Error in corpus_dao::deleteCorpus : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Removes from the DB a corpus and its associated tasks and sentences
   * 
   * @param int $corpus_id Corpus ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function removeCorpus($corpus_id){
      //Used when there are tasks associated to the corpus
    try {
      //First remove 
      $query1 = $this->conn->prepare("delete from sentences_pairing as sp using sentences as s where (sp.id_1 = s.id or sp.id_2 = s.id) and s.corpus_id = ?;");
      $query1->bindParam(1, $corpus_id);
      $query1->execute();

      //Then remove from sentences_tasks
      $query2 = $this->conn->prepare("delete from sentences_tasks using tasks where tasks.corpus_id = ? and sentences_tasks.task_id = tasks.id");
      $query2->bindParam(1, $corpus_id);
      $query2->execute();
      //Then remove from sentences
      $query3 = $this->conn->prepare("delete from sentences where corpus_id = ?");
      $query3->bindParam(1, $corpus_id);
      $query3->execute();
      //Remove from tasks
      $query4 = $this->conn->prepare("delete from tasks where corpus_id = ?");
      $query4->bindParam(1, $corpus_id);
      $query4->execute();
      //Finally delete corpus
      $query = $this->conn->prepare("DELETE FROM corpora WHERE id = ?;");
      $query->bindParam(1, $corpus_id);
      $query->execute();
      
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();   
      throw new Exception("Error in corpus_dao::removeCorpus : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Stores in the DB the number of lines of an uploaded corpus, after storing sentences is finished
   * 
   * @param int $corpus_id Corpus ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateLinesInCorpus($corpus_id) {
    try {
      $query = $this->conn->prepare("
        with counted as (select count(corpus_id) as count from sentences as s
        where corpus_id = ? and s.is_source = true group by corpus_id) 
        update corpora as c set lines = s.count from counted as s where c.id = ? returning lines;
      ");
      $query->bindParam(1, $corpus_id);
      $query->bindParam(2, $corpus_id);
      $query->execute();
      while($row = $query->fetch()){        
        $lines = $row['lines'];        
      }
      $this->conn->close_conn();      
      if ($lines == 0 || $lines=="" || $lines==null){
        //no inserted lines
        $this->deleteCorpus($this->id);
        return false;
      }
      else {
        return true;
      }
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in corpus_dao::updateLinesInCorpus : " . $ex->getMessage());
    }
    return false;
  }
  
    
  /**
   * Retrieves from the DB the requested amount of sentences from a given corpus
   * 
   * @param int $corpus_id  Corpus ID
   * @param int $amount Amount of sentences to retrieve
   * @return array Array of Sentence objects
   * @throws Exception
   */
  function getSentencesFromCorpus($corpus_id, $amount){
    $sentences = array();
    try {       
      $query = $this->conn->prepare(
        "select s1.* from sentences as s1
        where s1.corpus_id = ? and s1.is_source = true
        order by s1.id asc
        "
        . (isset($amount) ? " limit ?;" : ";")
      );
      
      $query->bindParam(1, $corpus_id);
      if (isset($amount)) $query->bindParam(2, $amount);
      $query->execute();

      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence = new sentence_dto();
        $sentence->id = $row['id'];
        $sentence->corpus_id = $row["corpus_id"];
        $sentence->source_text = $row["source_text"];
        $sentence->target_text = array();
        $sentence->type = $row["type"];

        $query2 = $this->conn->prepare("
          select s.* from sentences as s
          join sentences_pairing as sp on (s.id = sp.id_2)
          where sp.id_1 = ? order by id asc;
        ");
        $query2->bindParam(1, $sentence->id);
        $query2->execute();
        $query2->setFetchMode(PDO::FETCH_ASSOC);
        while ($row2 = $query2->fetch()) {
          $sentence_dto = new sentence_dto();
          $sentence_dto->id = $row2['id'];
          $sentence_dto->corpus_id = $row2['corpus_id'];
          $sentence_dto->source_text = $row2['source_text'];
          $sentence_dto->type = $row2['type'];
          $sentence_dto->system = $row2['system'];
  
          $sentence->target_text[] = $sentence_dto;
        }

        array_push($sentences, $sentence);
      }
      $this->conn->close_conn();
      return $sentences;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_dao::getSentencesFromCorpus : " . $ex->getMessage());
    }
  }

  /**
   * Updates in the DB the metadata of a given corpus
   * 
   * @param object $corpus_dto Corpus object
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function updateCorpus($corpus_dto){
     try {
      $query = $this->conn->prepare("UPDATE CORPORA SET name = ?, source_lang = ?, target_lang = ?, active =?  WHERE id = ?;");
      $query->bindParam(1, $corpus_dto->name);
      $query->bindParam(2, $corpus_dto->source_lang);
      $query->bindParam(3, $corpus_dto->target_lang);
      $query->bindParam(4, $corpus_dto->active);
      $query->bindParam(5, $corpus_dto->id);
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in corpus_dao::updateCorpus : " . $ex->getMessage());
    }
  }
}
/**
 * Datatables columns for the Corpus table
 */
corpus_dao::$columns = array(
    array('c.id', 'id'),
    array('c.name', 'name'),
    array('l1.langcode', 'source_lang'),
    array('l2.langcode', 'target_lang'),
    array('c.lines', 'lines'),
    array('c.creation_date', 'creation_date'),
    array('c.mode', 'mode'),
    array('c.active', 'active'),
    array('l1.langname', 'nsource_lang'),
    array('l2.langname', 'ntarget_lang')

);
