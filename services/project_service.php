<?php
/**
 * Project services.
 * Currently "new", that creates a new project and redirects to the Projects tab,
 *  and "list_dt", that serves the datatables content of the Projects table
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

const ERROR_CREATE_PROJECT = "Error while creating the project.";
const MISSING_PARAMETERS = "Missing parameters while saving the project: ";

$service = filter_input(INPUT_GET, "service");
if ($service == "new") {

  $failedparams = checkPostParameters(["name", "description"]);

  if (count($failedparams) == 0){
    $project_dto = new project_dto();

    $project_dto->name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $project_dto->description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $project_dto->owner = $USER->id;
    
    $project_dao = new project_dao();
    $result = $project_dao->insertProject($project_dto);
    if ($result) {
      header("Location: /admin/index.php#projects");
    }
    else {
      $_SESSION['error'] = ERROR_CREATE_PROJECT;
      $_SESSION['project'] = $project_dto;
      header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
    }
    die();
  }
  else {
    $_SESSION['error'] = MISSING_PARAMETERS . implode(', ', $failedparams);
    header("Location: " . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
    die();
  }
}
else if ($service == "list_dt") {
  $project_dao = new project_dao();
  echo $project_dao->getDatatablesProjects($_GET, getUserId());
  die();
}

// If no service requested or fail in params, redirect to home
header("Location: /index.php");
die();

