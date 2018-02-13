<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

const ERROR_CREATE_TASK = "Error while creating the task.";
const MISSING_PARAMETERS = "Missing parameters while saving the task: ";

$failedparams = checkPostParameters(["project", "assigned_user", "corpus"]);

if (count($failedparams) == 0){
  $task_dto = new task_dto();

  $task_dto->project_id = filter_input(INPUT_POST, "project", FILTER_SANITIZE_STRING);
  $task_dto->assigned_user = filter_input(INPUT_POST, "assigned_user", FILTER_SANITIZE_STRING);
  $task_dto->corpus_id = filter_input(INPUT_POST, "corpus", FILTER_SANITIZE_STRING);
  $datetime= date('Y-m-d H:i:s');
  $task_dto->assigned_date = $datetime;

  $result = false;
  $task_dao = new task_dao();
  if ($task_dao->insertTask($task_dto)) {
    $sentence_task_dao = new sentence_task_dao();
    $result = $sentence_task_dao->insertBatchSentencesFromCorpusToTask($task_dto->corpus_id, $task_dto->id);
    $task_dao->updateTaskSize($task_dto->id);
  }
  
  if ($result) {
    header("Location: /projects/project_manage.php?id=" . $task_dto->project_id);
  }
  else {
    // TODO Better to have an array with error => 0 and message => "..."
    $_SESSION['error'] = ERROR_CREATE_TASK;
    $_SESSION['project'] = $project_dto;
    header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
  }
}
else {
  $_SESSION['error'] = MISSING_PARAMETERS . implode(', ', $failedparams);
  header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
}