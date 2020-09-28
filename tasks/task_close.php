<?php
/**
 * Closes a task, and then redirects to the index page.
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/corpus_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_helper.class.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/templates/mail/closedtask.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

const ERROR_CREATE_TASK = "Error while closing the task.";
const MISSING_PARAMETERS = "Missing parameters while saving the task: ";

if (!isset($_GET["task_id"]) || ($_GET["task_id"])==""){
  $_SESSION['error'] = MISSING_PARAMETERS . implode(', ', "task_id");
  header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
}

//$task_dto = new task_dto();
$task_dao = new task_dao();

//$task_dto->id = $_GET["task_id"];
$task_dto = $task_dao->getTaskById($_GET["task_id"]);
$task_dto->completed_date = date('Y-m-d H:i:s');


//task admin o task assigned
if ($task_dto->assigned_user == $USER->id) {
  if ($task_dao->closeTask($task_dto)) {
    // We notify the project manager via mail

    $project_dao = new project_dao();
    $project_dto = $project_dao->getProjectById($task_dto->project_id);

    $user_dao = new user_dao();
    $owner = $user_dao->getUserById($project_dto->owner);
    $assigned_user = $user_dao->getUserById($task_dto->assigned_user);

    $corpus_dao = new corpus_dao();
    $corpus_dto = $corpus_dao->getCorpusById($task_dto->corpus_id);

    $params = (object) [
        "task_id" => $_GET["task_id"],
        "project_name" => $project_dto->name,
        "project_src_lang" => $task_dto->source_lang_object->langcode,
        "project_trg_lang" => $task_dto->target_lang_object->langcode,
        "assigned_user" => $assigned_user->name,
        "corpus_id" => $corpus_dto->id,
        "corpus_name" => $corpus_dto->name
    ];

    $mail = new MailHelper();
    $template = new ClosedTaskTemplate();
    $mail->prepare($template, $params);
    $mail->send($owner->email, $owner->name);

    if ($task_dto->mode == "ADE") {
      // We compute the Quality Control sentences according to Cambridge Core paper and then we save
      // https://www.cambridge.org/core/journals/natural-language-engineering/article/can-machine-translation-systems-be-evaluated-by-the-crowd-alone/E29DA2BC8E6B99AA1481CC92FAB58462/core-reader
      $sentence_dao = new sentence_dao();
      $sentence_task_dao = new sentence_task_dao();

      $sentences = $sentence_task_dao->getAnnotatedSentecesByTask($task_dto->id);
      $standard_scores = standarize($sentences);
      $wrong = 0;
      $control = 0;
      $repeated_sentences = array();
      foreach($sentences as $sentence) {
          $sentence_data = $sentence_dao->getSentenceById($sentence->sentence_id);
          if ($sentence_data->type == "bad_ref") {
              $control++;
              $control_scores["bad_ref"][] = $standard_scores[$sentence->sentence_id];
              if ($standard_scores[$sentence->sentence_id] > 1) $wrong++;
          } else if ($sentence_data->type == "ref") {
              $control++;
              $control_scores["ref"][] = $standard_scores[$sentence->sentence_id];
              if ($standard_scores[$sentence->sentence_id] < 1) $wrong++;
          } else if ($sentence_data->type == "rep") {
              $control++;
              $repeated_sentences[] = $sentence_task_dao->getSentenceByIdAndTask($sentence->id, $task_dto->id);
          }
      }
  
      for ($i = 0; $i < count($repeated_sentences); $i++) {
          $found = false;
          $rep = $repeated_sentences[$i];
  
          foreach ($sentences as $sentence) {
              if ($found) break;
              if ($rep->source_text == $sentence->source_text && $rep->id != $sentence->id) {
                  $control_scores["rep"][] = $standard_scores[$rep->sentence_id];
                  if (abs(intval($rep->evaluation) - intval($sentence->evaluation)) > 10) $wrong++;
                  $found = true;
              }
          }
      }
  
      $user_score_base = round((($control - $wrong) * 10) / $control, 2);
      $penalty = 0;
  
      // Detect extremely close mean scores
      $means = array();
      $keys = array_keys($control_scores);
      for ($i = 0; $i < count($keys); $i++) {
          $means[] = mean($control_scores[$keys[$i]]);
      }
      if(variance($means) < 0.05) $penalty += 0.1;
  
      // Score sequences with low variation
      $scores = array_values($standard_scores);
      $zone = array($scores[0]);
      $zone_start = 0;
      $zones = array();
      for ($i = 1; $i < count($scores); $i++) {
          $zone[] = $scores[$i];
          if (count($zone) > 1 && variance($zone) > 1) {
              $zones[] = array("start" => $zone_start, "values" => $zone);
              $zone_start = $i + 1;
              $zone = array();
          }
      }
      
      $large_zones = 0;
      foreach ($zones as $zone) {
          if (count($zone["values"]) > (count($sentences) * 0.15)) $large_zones++;
      }
  
      $penalty += (($large_zones / count($zones)) * 0.1);
    
      $user_score = (1 - $penalty) * $user_score_base;
      $task_dao->setTaskScore($task_dto->id, $user_score);
    }

    header("Location: /tasks/recap.php?id=" . $task_dto->id);
  } else {
    // TODO Better to have an array with error => 0 and message => "..."
    //$_SESSION['error'] = ERROR_CREATE_TASK;
    //$_SESSION['project'] = $project_dto;
    //header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
      header("Location: /index.php");
  }
} else {
    // TODO Better to have an array with error => 0 and message => "..."
    //$_SESSION['error'] = ERROR_CREATE_TASK;
   // $_SESSION['project'] = $project_dto;
    //header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
    header("Location: /index.php");
  }

