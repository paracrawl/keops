<?php
/**
 * Updates a task (currently, only removing them) and then redirects to the "Projects" tab
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");



if (isset($_POST["id"]) && isset($_POST["action"]) && $_POST["action"] == "remove") {
  //It's a task removal
  
  $task_id = $_POST["id"];
  $task_dao = new task_dao();
  if ($task_dao->removeTask($task_id)) {
    $_SESSION["error"] = null;
    header("Location: /admin/index.php#projects");
    die();
  } else {
    $_SESSION["error"] = "taskremoveerror";
    header("Location: /admin/index.php#projects");
    die();
  }
} if (isset($_POST["id"]) && isset($_POST["project_id"]) && isset($_POST["action"]) && $_POST["action"] == "reassign" && isset($_POST["assigned_user"])) {
  // It's a task reassignment
  $task_id = $_POST["id"];
  $assigned_user = $_POST["assigned_user"];
  $task_dao = new task_dao();
  if ($task_dao->updateAssignedUser($task_id, $assigned_user)) {
    $_SESSION["error"] = null;
    header("Location: /projects/project_manage.php?id=" . $_POST["project_id"]);
  } else {
    $_SESSION["error"] = "taskreassignerror";
    header("Location: /admin/index.php#projects");
    die();  
  }
} else {
  //Currently, no other actions than removing or reassigning tasks
  $_SESSION["error"] = "missingparams";
  header("Location: /admin/index.php#projects");
  die();
}