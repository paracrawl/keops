<?php

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
      }
      $this->conn->close_conn();
      return $corpus;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in corpus_dao::getCorpusById : " . $ex->getMessage());
    }
  }
  
  /** 
   * 
   * @param array $filters Expected map with keys (name of rows) and values to filter.
   */
  function getFilteredCorpora($filters) {
    try {
      $sql = "SELECT id, name, source_lang, target_lang FROM corpora";
      if (count($filters) > 0) {
        $where = array();
        foreach ($filters as $key => $value) {
          $where[] = $key . "=" . $value . " ";
        }
        $sql .= " where " . implode(" AND ", $where);
      }
      
      $corpora = array();
      $query = $this->conn->prepare($sql);
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
          
  function getDatatablesCorpora($request) {
    try {
      return json_encode(DatatablesProcessing::simple( $request, $this->conn,
              "corpora as c left join langs as l1 on c.source_lang = l1.id left join langs as l2 on c.target_lang = l2.id",
              "c.id",
              self::$columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in corpus_dao::getDatatablesCorpora : " . $ex->getMessage());
    }
  }
  
  function insertCorpus($corpus_dto) {
    try {
      $query = $this->conn->prepare("INSERT INTO corpora (name, source_lang, target_lang) VALUES (?, ?, ?);");
      $query->bindParam(1, $corpus_dto->name);
      $query->bindParam(2, $corpus_dto->source_lang);
      $query->bindParam(3, $corpus_dto->target_lang);
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
  
  //Used ONLY when upload fails
  function deleteCorpus($corpus_id){
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
  
  //Used when there are tasks associated to the corpus
  function removeCorpus($corpus_id){
    try {
      //First remove from sentences_tasks
      $query1 = $this->conn->prepare("delete from sentences_tasks using tasks where tasks.corpus_id = ? and sentences_tasks.task_id = tasks.id");
      $query1->bindParam(1, $corpus_id);
      $query1->execute();
      //Then remove from sentences
      $query2 = $this->conn->prepare("delete from sentences where corpus_id = ?");
      $query2->bindParam(1, $corpus_id);
      $query2->execute();
      //Remove from tasks
      $query3 = $this->conn->prepare("delete from tasks where corpus_id = ?");
      $query3->bindParam(1, $corpus_id);
      $query3->execute();
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
  
  function updateLinesInCorpus($corpus_id) {
    try {
      $query = $this->conn->prepare("with counted as (select count(corpus_id) as count from sentences where corpus_id = ? group by corpus_id) update corpora as c set lines = s.count from counted as s where c.id = ? returning lines;");
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
  
    
  function getSentencesFromCorpus($corpus_id, $amount){
    $sentences = array();
    try {       
      $query = $this->conn->prepare("select * from sentences where corpus_id = ? order by id limit ?;");
      $query->bindParam(1, $corpus_id);
      $query->bindParam(2, $amount);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence = new sentence_dto();
        $sentence->id = $row['id'];
        $sentence->corpus_id = $row["corpus_id"];
        $sentence->source_text = $row["source_text"];
        $sentence->target_text = $row["target_text"];
        array_push($sentences, $sentence);
      }
      $this->conn->close_conn();
      return $sentences;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in sentence_dao::getSentencesFromCorpus : " . $ex->getMessage());
    }
  }
  
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
corpus_dao::$columns = array(
    array( 'db' => 'c.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'c.name', 'alias' => 'name', 'dt' => 1 ),
    array( 'db' => 'l1.langcode', 'alias' => 'source_lang', 'dt' => 2 ),
    array( 'db' => 'l2.langcode', 'alias' => 'target_lang', 'dt' => 3 ),
    array( 'db' => 'c.lines', 'alias' => 'lines', 'dt' => 4 ),
    array( 'db' => 'c.creation_date', 'alias' => 'creation_date', 'dt' => 5,
        'formatter' => function ($d, $row) { return getFormattedDate($d); } ),
    array( 'db' => 'c.active', 'alias' => 'active', 'dt' => 6 ),
    array( 'db' => 'l1.langname', 'alias' => 'nsource_lang', 'dt' => 7 ),
    array( 'db' => 'l2.langname', 'alias' => 'ntarget_lang', 'dt' => 8 )
);
