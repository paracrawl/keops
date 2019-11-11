<?php
/**
 * Saves the evaluation of a sentence, and retrieves the next one
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/comment_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

const ERROR_SAVE_EVALUATION = "Error while saving the evaluation.";
const MISSING_PARAMETERS = "Missing parameters while saving the sentence: ";

$failedparams = checkPostParameters(["task_id", "sentence_id", "evaluation"]);

if (count($failedparams) == 0){
  $task_dao = new task_dao();
  $task_id = filter_input(INPUT_POST, "task_id");
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->status != "DONE" && $task->assigned_user == $USER->id) {
    $sentence_task_dto = new sentence_task_dto();

    $sentence_task_dto->id = filter_input(INPUT_POST, "sentence_id", FILTER_SANITIZE_STRING);
    $sentence_task_dto->task_id = filter_input(INPUT_POST, "task_id", FILTER_SANITIZE_STRING);
    $sentence_task_dto->evaluation = filter_input(INPUT_POST, "evaluation");
    
    $start_time = filter_input(INPUT_POST, "time");
    $datetime = new DateTime();
    $end_time = $datetime->getTimestamp();

    $sentence_task_dto->time = ($end_time - $start_time);

    $datetime= date('Y-m-d H:i:s');
    $sentence_task_dto->completed_date = $datetime;

    $sentence_task_dao = new sentence_task_dao();
    if ($sentence_task_dao->updateSentence($sentence_task_dto)) {
      if ($task->status == "PENDING"){
        $task_dao->startTask($task_id);
      }

      $comment_dao = new comment_dao();
      $comment_dao->clearComments($sentence_task_dto->id);

      $personal_data = filter_input(INPUT_POST, "personal_data");
      $content_error = filter_input(INPUT_POST, "content_error");

      if (isset($personal_data)) $comment_dao->upsertComment(comment_dto::newComment($sentence_task_dto->id, "personal_data", ($personal_data == "on")));
      if (isset($content_error)) $comment_dao->upsertComment(comment_dto::newComment($sentence_task_dto->id, "content_error", ($content_error == "on")));

      $keys = array_keys($_POST);
      foreach (array_keys($_POST) as $key) {
        $matches = array();
        if(preg_match("/^(". $sentence_task_dto->evaluation ."_.*)/", $key, $matches)) {
          $comment_dao->upsertComment(comment_dto::newComment($sentence_task_dto->id, $matches[1], $_POST[$key]));
        }
      }

      $search_term = filter_input(INPUT_POST, "term");
      $label = filter_input(INPUT_POST, "label");
      $p_id = filter_input(INPUT_POST, "p_id");
      $str = "Location: /sentences/evaluate.php?task_id=" . $sentence_task_dto->task_id . "&p=1&id=" . ($p_id+1) . ((isset($search_term) && isset($label)) ? "&term=".$search_term."&label=".$label : "");
      header($str);
      die();
    }
    else {
      // TODO Better to have an array with error => 0 and message => "..."
      $_SESSION['error'] = ERROR_SAVE_EVALUATION;
      header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
      die();
    }
  }
  else {
    // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
    // Message: You don't have access to this evaluation / We couldn't find this task for you
    header("Location: /index.php");
    die();
  }
}
else {
  $_SESSION['error'] = MISSING_PARAMETERS . implode(', ', $failedparams);
  header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
  die();
}