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
   * $source_lang, $target_lang, $data, $mode = "", $count = 2
   * @param int $source_lang  ID of the source lang
   * @param int $target_lang  ID of the target lang
   * @param array $data Array containing the sentences
   * @param string  $mode The mode of the uploaded corpus (ADE, FLU, VAL or RAN)
   * @param string  $count  Amount of columns 
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  function insertBatchSentences($corpus_id, $source_lang, $target_lang, $data, $mode = "", $count = 2) {
    try {
      $insert_values = array();
      $batch_size = 1000;
      $batches = ceil(count($data) / 1000);
      $group_size = 0;

      $pairs = array();
      for ($i = 0; $i < $batches; $i++) {
        $batch = array_slice($data, $i * $batch_size, ($i + 1) * $batch_size);
        $paramvalues = array();

        $query_str = "INSERT INTO sentences (corpus_id, source_text, source_text_vector, type, is_source, system) VALUES ";
        foreach($batch as $d) {
          if ($mode == "ADE" || $mode == "RAN") {
            $_d = $d;
            $type = $d[1];
            $d = $d[0];
          } else {
            $type = "";
          }
  
          $is_source = ($mode == "FLU") ? "true" : "false";

          $pair = array();
          foreach ($d as $sentence) {
            $query_str = $query_str . "(?, ?, to_tsvector('simple', ?), ?, ?, ?),";
            $paramvalues[] = $corpus_id;
            $paramvalues[] = $sentence;
            $paramvalues[] = $sentence;
            $paramvalues[] = $type;
            $paramvalues[] = $is_source;
            $paramvalues[] = ($type == "ranking") ? $_d[2] : NULL;
          }
        }

        $query = $this->conn->prepare(substr_replace($query_str, "RETURNING id", -1));
        $query->execute($paramvalues);
        
        if ($mode == "RAN") {
          while($row = $query->fetch()){        
            $pair[] = $row['id'];
          }

          if (count($pair) > 1) $pairs = array_merge($pairs, $pair);

        } else if ($mode != "FLU") {
          while($row = $query->fetch()){        
            $pair[] = $row['id'];
          }

          if (count($pair) > 1) $pairs = array_merge($pairs, $pair);
        }
      }

      $batches = ceil(count($pairs) / 1000);
      for ($i = 0; $i < $batches; $i++) {
        $batch_data =  array_slice($pairs, $i * $batch_size, ($i + 1) * $batch_size);

        $query1_str = "insert into sentences_pairing(id_1, id_2) values ";
        $query1_values = array();
        
        $query2_str = "update sentences set is_source = true where ";
        $query2_values = array();

        for ($j = 0; $j < count($batch_data); $j += $count) {
          for ($k = $j + 1; $k < ($j + $count); $k++) {
            $query1_str = $query1_str . ("(?, ?),");
            $query1_values[] = $batch_data[$j];
            $query1_values[] = $batch_data[$k];
          }
          
          $query2_str = $query2_str . "id = ? or ";
          $query2_values[] = $batch_data[$j];
        }

        $query1 = $this->conn->prepare(substr_replace($query1_str, "", -1));
        $query1->execute($query1_values);

        $query2 = $this->conn->prepare(substr_replace($query2_str, "", -4));
        $query2->execute($query2_values);
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
  public function insertSentence($corpus_id, $source_lang, $target_lang, $text, $type = "", $system = null) {
    try {
      $query = $this->conn->prepare("INSERT INTO sentences (corpus_id, source_text, source_text_vector, type, system) VALUES (?, ?, to_tsvector('simple', ?), ?, ?) returning id");
      $query->bindParam(1, $corpus_id);
      $query->bindParam(2, $text);
      $query->bindParam(3, $text);
      $query->bindParam(4, $type);
      $query->bindParam(5, $system);

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
