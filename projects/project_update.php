<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");



if (isset($_POST["id"]) && isset($_POST["action"]) && $_POST["action"] == "remove") {
  //It's a project removal
  $project_id = $_POST["id"];
  $project_dao = new project_dao();
  if ($project_dao->removeProject($project_id)) {
    $_SESSION["error"] = null;
    header("Location: /admin/index.php#projects");
    die();
  } else {
    $_SESSION["error"] = "projectremoveerror";
    header("Location: /admin/index.php#projects");
    die();
  }
} else {
  $failedparams = checkPostParameters(["id", "name", "source_lang", "target_lang", "description"]);

  if (count($failedparams) == 0) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $source_lang = $_POST["source_lang"];
    $target_lang = $_POST["target_lang"];
    $description = $_POST["description"];
    $active = "false";


    if (isset($_POST["active"]) && $_POST["active"] == "on") {
      $active = "true";
    }

    $project_dao = new project_dao();
    $project_dto = project_dto::newProject($id, $name, $source_lang, $target_lang, $description, $active);

    if ($project_dao->updateProject($project_dto)) {
      $_SESSION["error"] = null;
      header("Location: /admin/index.php#projects");
      die();
    } else {
      $_SESSION["error"] = "projectediterror";
      header("Location: /admin/index.php#projects");
      die();
    }
  } else {
    $_SESSION["error"] = "missingparams";
    header("Location: /admin/index.php#projects");
    die();
  }
}