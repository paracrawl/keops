<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
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
    header("Location: /index.php");
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

