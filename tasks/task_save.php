<?php
/**
 * Creates a new task, then redirects to the Project manage page
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_helper.class.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/templates/mail/assignedtask.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

const ERROR_CREATE_TASK = "Error while creating the task.";
const MISSING_PARAMETERS = "Missing parameters while saving the task: ";

$failedparams = checkPostParameters(["project", "assigned_user", "corpus", "source_lang", "target_lang"]);

if (count($failedparams) == 0){
  $assigned_user = filter_input(INPUT_POST, "assigned_user", FILTER_SANITIZE_STRING);

  $task_dto = new task_dto();

  $task_dto->project_id = filter_input(INPUT_POST, "project", FILTER_SANITIZE_STRING);
  $task_dto->assigned_user = $assigned_user;
  $task_dto->corpus_id = filter_input(INPUT_POST, "corpus", FILTER_SANITIZE_STRING);
  $datetime= date('Y-m-d H:i:s');
  $task_dto->assigned_date = $datetime;
  $task_dto->source_lang = filter_input(INPUT_POST, "source_lang", FILTER_SANITIZE_STRING);
  $task_dto->target_lang = filter_input(INPUT_POST, "target_lang", FILTER_SANITIZE_STRING);
  $task_dto->mode = filter_input(INPUT_POST, "mode", FILTER_SANITIZE_STRING);

  $result = false;
  $task_dao = new task_dao();
  if ($task_dao->insertTask($task_dto)) {
    $sentence_task_dao = new sentence_task_dao();
    $result = $sentence_task_dao->insertBatchSentencesFromCorpusToTask($task_dto->corpus_id, $task_dto->id);
    $task_dao->updateTaskSize($task_dto->id);
  }
  
  if ($result) {
    // We notify via email about the assigned task
    $user_dao = new user_dao();
    $user = $user_dao->getUserById($assigned_user);
    $mail = new MailHelper();
    $template = new AssignedTaskTemplate();
    $mail->prepare($template, $task_dto);
    $mail->send($user->email, $user->name);

    header("Location: /projects/project_manage.php?id=" . $task_dto->project_id);
  }
  else {
    // TODO Better to have an array with error => 0 and message => "..."
//    $_SESSION['error'] = ERROR_CREATE_TASK;
//    $_SESSION['project'] = $project_dto;
//    header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
      $_SESSION["error"] = "errorcreatingtask";
      header("Location: /projects/project_manage.php?id=" . $task_dto->project_id);
  }
}
else {
  $_SESSION['error'] = MISSING_PARAMETERS . implode(', ', $failedparams);
  header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
}