<?php
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/corpus_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_dao.php");
$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

try {
  if (!empty($_FILES)) {
      $tempFile = $_FILES['file']['tmp_name'];
      error_log($tempFile);
      error_log(implode(", ", $_FILES['file']));
      error_log(filter_input(INPUT_POST, "source_lang"));
      error_log(filter_input(INPUT_POST, "target_lang"));
      $corpus_dto = new corpus_dto();
      $corpus_dto->name = $_FILES['file']['name'];
      $corpus_dto->source_lang = filter_input(INPUT_POST, "source_lang");
      $corpus_dto->target_lang = filter_input(INPUT_POST, "target_lang");

      $corpus_dao = new corpus_dao();
      $corpus_dao->insertCorpus($corpus_dto);
      $handle = @fopen($tempFile, "r"); //read line one by one
      $values = array();

      $sentence_dao = new sentence_dao();

      while (!feof($handle)) // Loop 'til end of file.
      {
          $buffer = fgets($handle); // Read a line.
          $data = str_getcsv($buffer, "\t");
          if (!empty(trim($buffer)) && count($data) == 2 && strlen($data[0]) <= 5000 && strlen($data[1]) <= 5000) {
            $values[] = $data;// save values
          }
          else {
            error_log("WARNING : The following line of the file " . $_FILES['file']['name'] . " is not allowed : '" . $buffer . "'");
            continue;
          }

          // Save 1000 rows at the same time at most
          if (count($values) == 1000) {
            $result = $sentence_dao->insertBatchSentences($corpus_dto->id, $values);
            if ($result) {
              $values = array();
            }
          }
      }
      if (count($values) > 0) {
        $result = $sentence_dao->insertBatchSentences($corpus_dto->id, $values);
      }
      $corpus_dao->updateLinesInCorpus($corpus_dto->id);

      fclose($handle);
      //header("HTTP/1.1 400 Bad Request");
      //echo "Ups error message";
  }
} catch (Exception $ex) {
  error_log($ex->getMessage());
  header("HTTP/1.1 500 Server error");
  echo "Oops! An error ocurred on server side, please contact with administrators.";
}