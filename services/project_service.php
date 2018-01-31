<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
require_once(RESOURCES_PATH . "/session.php");

$service = filter_input(INPUT_GET, "service");
if ($service == "new") {

  $failedparams = checkPostParameters(["name", "source_lang", "target_lang", "description"]);

  if (count($failedparams) == 0){
    $project_dto = new project_dto();

    $project_dto->name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $project_dto->source_lang = filter_input(INPUT_POST, "source_lang", FILTER_SANITIZE_STRING);
    $project_dto->target_lang = filter_input(INPUT_POST, "target_lang", FILTER_SANITIZE_STRING);
    $project_dto->description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
    $project_dto->task_size = filter_input(INPUT_POST, "task_size", FILTER_SANITIZE_STRING);
    $project_dto->owner = $USER->id;
    
    $project_dao = new project_dao();
    echo $project_dao->insertProject($project_dto);
    die();
  }
}
else if ($service == "list_dt") {
  $project_dao = new project_dao();
  echo $project_dao->getDatatablesProjects($_GET);
  die();
}

// If no service requested or fail in params, redirect to home
header("Location: /index.php");
die();

