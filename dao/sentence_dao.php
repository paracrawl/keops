<?php
/**
 * Methods to work with Sentence objects and the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class sentence_dao {
  private $conn;
  //public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  /**
   * Inserts into the DB a batch of sentences, and associates them to the corpus they belong
   * 
   * @param int $corpus_id Corpus ID
   * @param array $data Array containing the sentences
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function insertBatchSentences($corpus_id, $source_lang, $target_lang, $data, $mode = "") {
    try {
      $insert_values = array();
      foreach($data as $d){
        if ($mode == "ADE") {
          $type = $d[1];
          $d = $d[0];
        } else {
          $type = "";
        }

        foreach ($d as $sentence) {
          $query = $this->conn->prepare("INSERT INTO sentences (corpus_id, source_text, source_text_vector, type, is_source) VALUES (?, ?, to_tsvector('simple', ?), ?, false)");
          $query->bindParam(1, $corpus_id);
          $query->bindParam(2, $sentence);
          $query->bindParam(3, $sentence);
          $query->bindParam(4, $type);

          $query->execute();
        }

        if (count($d) > 1) {
          $query = $this->conn->prepare("insert into sentences_pairing(id_1, id_2) select id[2], id[1] from 
          (select array_agg(id) as id
          from (select s.id as id from sentences as s order by s.id desc limit 2) as a) as b;");
          $query->execute();

          $query = $this->conn->prepare("update sentences set is_source = true from
          (select id[2] as source from
           (select array_agg(id) as id
           from (select s.id as id from sentences as s order by s.id desc limit 2) as a) as b) as c
          where id = c.source;");
          $query->execute();
        }
      }

      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();      
      throw new Exception("Error in sentence_dao::insertBatchSentences : " . $ex->getMessage());
    }
    return false;
  }

  /**
   * Inserts  a sentence into the DB and associates them to the corpus they belong
   * 
   * @param int $corpus_id Corpus ID
   * @param array $data Array containing the sentences
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  public function insertSentence($corpus_id, $source_lang, $target_lang, $text, $type = "") {
    try {
      $query = $this->conn->prepare("INSERT INTO sentences (corpus_id, source_text, source_text_vector, type) VALUES (?, ?, to_tsvector('simple', ?), ?) returning id");
      $query->bindParam(1, $corpus_id);
      $query->bindParam(2, $text);
      $query->bindParam(3, $text);
      $query->bindParam(4, $type);

      $query->execute();

      while($row = $query->fetch()){        
        $id = $row['id'];        
      }

      $this->conn->close_conn();
      return $id;
    } catch (Exception $ex) {
      $this->conn->close_conn();      
      throw new Exception("Error in sentence_dao::insertBatchSentences : " . $ex->getMessage());
    }
    return false;
  }

    /**
   * Pairs two sentences in the DB
   * 
   * @param int $sentence1 ID of sentence 1
   * @param int $sentence2 ID of sentence 2
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  public function pairSentences($sentence1, $sentence2) {
    try {
      $query = $this->conn->prepare("INSERT INTO sentences_pairing (id_1, id_2) values (?, ?)");
      $query->bindParam(1, $sentence1);
      $query->bindParam(2, $sentence2);
      $query->execute();

      $query = $this->conn->prepare("update sentences set is_source = true where id = ?");
      $query->bindParam(1, $sentence1);
      $query->execute();

      $query = $this->conn->prepare("update sentences set is_source = false where id = ?");
      $query->bindParam(1, $sentence2);
      $query->execute();

      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();      
      throw new Exception("Error in sentence_dao::insertBatchSentences : " . $ex->getMessage());
    }
    return false;
  }


  /**
   * Gets a sentence given its ID
   * 
   * @param int $sentence_id Sentence ID
   * @return \sentence_dto Sentence object
   * @throws Exception
   */
  public function getSentenceById($sentence_id) {
    try {
      $query = $this->conn->prepare("select * from sentences where id = ?");
      $query->bindParam(1, $sentence_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);

      $sentence_dto = new sentence_dto();
      while ($row = $query->fetch()) {
        $sentence_dto->id = $row['id'];
        $sentence_dto->corpus_id = $row['corpus_id'];
        $sentence_dto->source_text = $row['source_text'];
        $sentence_dto->target_text = array();
        $sentence_dto->type = $row['type'];
      }

      $query = $this->conn->prepare("
        select s.source_text as target_text from sentences as s
        join sentences_pairing as sp on (s.id = sp.id_2)
        where sp.id_1 = ?;
      ");
      $query->bindParam(1, $sentence_dto->id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while ($row = $query->fetch()) {
        $sentence_task_dto->target_text[] = $row['target_text'];
      }

      $this->conn->close_conn();
      return $sentence_dto;
    } catch (Exception $ex) {
      $this->conn->close_conn();      
      throw new Exception("Error in sentence_dao::getSentenceById : " . $ex->getMessage());
    }
    return false;
  }  
}
