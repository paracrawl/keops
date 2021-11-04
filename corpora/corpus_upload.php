<?php
/**
 * Uploads a corpus
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/corpus_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/language_dao.php");
$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

class CorpusException extends Exception{ }

try {
  if (empty($_FILES)) return;

  $mode = filter_input(INPUT_POST, "mode");

  $tempFile = $_FILES['file']['tmp_name'];
  $corpus_dto = new corpus_dto();
  $corpus_dto->name = $_FILES['file']['name'];
  $corpus_dto->source_lang = filter_input(INPUT_POST, "source_lang");
  $corpus_dto->target_lang = filter_input(INPUT_POST, "target_lang");  
  $corpus_dto->mode = filter_input(INPUT_POST, "mode");
  $corpus_dto->added_by = getUserId();

  /*
    Autodetect language
    Possible name formats:
      [filename].[src].[trg]
      [filename].[src]-[trg]

    ** For fluency: **
      [filename].[trg]
  */
  $lang_dao = new language_dao();
  $matches = [];

  if ($corpus_dto->mode == "FLU") {
    if (preg_match('/^.+\.(\w{2})$/', $corpus_dto->name, $matches)) {
      if (count($matches) > 1 && $lang_dao->existsLangCode($matches[1])) {
        $corpus_dto->target_lang = $lang_dao->getLangByLangCode($matches[1])->id;
      }
    }
  } else if (preg_match('/^.+\.(\w{2})[\.-](\w{2})$/', $corpus_dto->name, $matches)) {
    if (count($matches) > 2 && $lang_dao->existsLangCode($matches[1]) && $lang_dao->existsLangCode($matches[2])) {
      $corpus_dto->source_lang = $lang_dao->getLangByLangCode($matches[1])->id;
      $corpus_dto->target_lang = $lang_dao->getLangByLangCode($matches[2])->id;
    }
  }

  $corpus_dao = new corpus_dao();

  $sentence_dao = new sentence_dao();
  $first_batch = true;
  
  $corpus_dao->insertCorpus($corpus_dto);
  if (intval($corpus_dto->id) < 0){
    $corpus_dao->deleteCorpus($corpus_dto->id);
    throw new CorpusException("Invalid format of corpus uploaded.");
  }

  if ($mode == "VAL" || $mode == "FLU" || $mode == "PAR") {
    file_reader($tempFile, (in_array($mode, ["VAL", "PAR"])) ? 2 : 1, function($values) use ($sentence_dao, $corpus_dto, $corpus_dao, $mode) {
      $result = $sentence_dao->insertBatchSentences($corpus_dto->id,
          $corpus_dto->source_lang, $corpus_dto->target_lang, $values,
          $mode, (in_array($mode, ["VAL", "PAR"])) ? 2 : 1);
      $corpus_dao->updateLinesInCorpus($corpus_dto->id);
    }, 1000, true);
  } else if ($mode == "ADE") {
    $shouldHaveControl = filter_input(INPUT_POST, "shouldHaveControl");
    error_log("shouldHaveControl $shouldHaveControl");

    if (empty($shouldHaveControl)) {
      file_reader($tempFile, 2, function($values) use ($sentence_dao, $corpus_dto, $corpus_dao, $mode) {
        $sentencesWithType = array();
        foreach ($values as $sentence) {
          $sentencesWithType[] = array($sentence, 'legit');
        }

        $result = $sentence_dao->insertBatchSentences($corpus_dto->id, $corpus_dto->source_lang, $corpus_dto->target_lang, $sentencesWithType, $mode, 2);
        $corpus_dao->updateLinesInCorpus($corpus_dto->id);
      }, 1000, true);
    } else {
      file_reader($tempFile, 2, function ($values) use ($corpus_dto, $corpus_dao, $sentence_dao, $mode) {
        $total = count($values);

        if ($total == 0) throw new CorpusException("Invalid format of corpus uploaded.");
        $final_total = (100 / 70) * $total;

        // Reference sentences
        $ref_group = array();
        $ref_total = floor($final_total * 0.1);
        $ref_group_total = floor($total / 3) + $ref_total;
        $legit_in_ref = $ref_group_total - $ref_total;

        for ($i = 0; $i < $legit_in_ref; $i++) {
          $ref_group[] = array($values[0], 'legit');
          array_splice($values, 0, 1);
        }

        for ($i = 0; $i < $ref_total; $i++) {
          $sentence = $values[mt_rand(0, count($values) - 1)];
          $sentence[1] = $sentence[0];
          $ref_group[] = array($sentence, 'ref');
        }

        // bad_reference sentences
        $bad_ref_group = array();
        $bad_ref_total = floor($final_total * 0.1);
        $bad_ref_group_total = floor($total / 3) + $bad_ref_total;
        $legit_in_bad_ref = $bad_ref_group_total - $bad_ref_total;

        for ($i = 0; $i < $legit_in_bad_ref; $i++) {
          $bad_ref_group[] = array($values[0], 'legit');
          array_splice($values, 0, 1);
        }

        for ($i = 0; $i < $bad_ref_total; $i++) {
          $sentence_pair = $values[0];
          $sentence = $sentence_pair[1];
          $words = explode(" ", $sentence);
          $c = count($words);
          $remove = ($c < 4) ? 1 : ($c < 6) ? 2 : ($c < 9) ? 3 : ($c < 16) ? 5 : floor($c / 5);
          for ($j = 0; $j < $remove; $j++) {
            $p = mt_rand(0, $c);
            array_splice($words, $p, 1);
            $c = count($words);
          }

          $sentence_pair[1] = implode(" ", $words);
          $bad_ref_group[] = array($sentence_pair, 'bad_ref');
        }

        // repeated sentences
        $repeated_group = array();
        $repeated_total = floor($final_total * 0.1);
        $repeated_group_total = floor($total / 3) + $repeated_total + ceil($total % 3);
        $legit_in_repeated = $repeated_group_total - $repeated_total;

        $added = array();

        for ($i = 0; $i < $legit_in_repeated; $i++) {
          $repeated_group[] = array($values[0], 'legit');
          $added[] = $values[0];
          array_splice($values, 0, 1);
        }

        for ($i = 0; $i < $repeated_total; $i++) {
          $pos_added = mt_rand(0, count($added) - 1);
          $repeated_group[] = array($added[$pos_added], 'rep');
          array_splice($added, $pos_added, 1);
        }

        // We are ready to save
        shuffle($ref_group);
        shuffle($bad_ref_group);
        shuffle($repeated_group);
        $sentences = array_merge($ref_group, array_merge($bad_ref_group, $repeated_group));
        $result = $sentence_dao->insertBatchSentences($corpus_dto->id, $corpus_dto->source_lang, $corpus_dto->target_lang, $sentences, $mode);
        if ($result) {
          $corpus_dao->updateLinesInCorpus($corpus_dto->id);
        }
      }, null, true);
    }
  } else if ($mode == "RAN") {
    file_reader($tempFile, -1, function($values, $headers, $count) use ($sentence_dao, $corpus_dao, $corpus_dto, $mode) {
      // We are ready to save
      if (count($values) > 0) {
        $data = array();
        foreach ($values as $group) {
          $data[] = array(array($group[0]), "source");
          $data[] = array(array($group[1]), "reference");

          for ($i = 2; $i < count($group); $i++) {
            $data[] = array(array($group[$i]), "ranking", $headers[$i]);
          }
        }

        $result = $sentence_dao->insertBatchSentences($corpus_dto->id, $corpus_dto->source_lang, $corpus_dto->target_lang, $data, $mode, $count);
        if ($result) {
          $corpus_dao->updateLinesInCorpus($corpus_dto->id);
        }
      } else {
        throw new CorpusException("Invalid format of corpus uploaded.");
      }
    }, 1000, true);
  }
} catch (CorpusException $ex){
  error_log($ex->getMessage());
  header("HTTP/1.1 500 Server error");
  echo "Oops! The corpus you tried to upload is invalid.";  
} catch (Exception $ex) {
  error_log($ex->getMessage());
  header("HTTP/1.1 500 Server error");
  echo "Oops! An error ocurred on server side, please contact with administrators.";
}


/**
 * Given a filename, reads everyline and extracts $count sentences.
 * If the number of read lines exceeds $batch_size, $callback is called
 * with those lines and then they are discarded.
 * 
 * @param string $filename Name of the file to read
 * @param int $count Amount of sentences (-1 for until the size of the first sentence)
 * @param Closure $callback Function to run when a batch is available
 * @param int $batch_size Size of the batch
 */
function file_reader($filename, $count, $callback, $batch_size = null, $has_headers = false) {
  $handle = @fopen($filename, "r");
  $values = array();
  $headers = array();
  try {
    while (!feof($handle)) {
      $buffer = fgets($handle);
      $buffer =  preg_replace("/\r|\n/", "", $buffer);
      $data = explode("\t", $buffer);

      if ($has_headers) {
        $headers = $data;
        $has_headers = false;
        continue;
      }

      $count = ($count == -1) ? count($data) : $count;
      $data = array_slice($data, 0, $count);

      $valid = !empty(trim($buffer)) && count($data) == $count;
      for ($i = 0; $valid && $i < count($data); $i++) {
        $valid = $valid && (strlen($data[$i]) <= 5000);
      }

      if ($valid) {
        $values[] = $data;
      } else {
        error_log("WARNING : The following line of the file " . $_FILES['file']['name'] . " is not allowed : '" . $buffer . "'");
        continue;
      }

      if (isset($batch_size) && count($values) >= $batch_size) {
        $callback($values, $headers, $count);
        $values = array();
      }
    }

    if (count($values) > 0) $callback($values, $headers, $count);
  } catch (Exception $e) {
    return [];
  } finally {
    fclose($handle);
  }
}