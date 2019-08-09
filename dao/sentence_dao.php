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
  function insertBatchSentences($corpus_id, $source_lang, $target_lang, $data) {
    try {
      $insert_values = array();
      foreach($data as $d){
          $question_marks[] = '('  . rtrim(str_repeat('?,', sizeof($d) + 1), ",") . ", to_tsvector('simple', ?), to_tsvector('simple', ?) )"; // +3 for id
          $insert_values[] = $corpus_id;
          $insert_values = array_merge($insert_values, array_values($d));
          $insert_values[] = $d[0];
          $insert_values[] = $d[1];
      }

      $query = $this->conn->prepare("INSERT INTO sentences (corpus_id, source_text, target_text, source_text_vector, target_text_vector) VALUES " . implode(',', $question_marks));
      $query->execute($insert_values);
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();      
      throw new Exception("Error in sentence_dao::insertBatchSentences : " . $ex->getMessage());
    }
    return false;
  }

}
