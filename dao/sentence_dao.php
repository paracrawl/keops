<?php

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
  
  
  function insertBatchSentences($corpus_id, $data) {
    try {
      $insert_values = array();
      foreach($data as $d){
          $question_marks[] = '('  . rtrim(str_repeat('?,', sizeof($d) + 1), ",") . ')'; // +1 for id
          $insert_values[] = $corpus_id;
          $insert_values = array_merge($insert_values, array_values($d));
      }
      
      $query = $this->conn->prepare("INSERT INTO sentences (corpus_id, source_text, target_text) VALUES " . implode(',', $question_marks));
      $query->execute($insert_values);
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      throw new Exception("Error in sentence_dao::insertBatchSentences : " . $ex->getMessage());
    }
    return false;
  }
}
