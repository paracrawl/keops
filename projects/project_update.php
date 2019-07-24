<?php
/**
 * Updates or removes a project and then redirects to Projects tab
 */
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
  $failedparams = checkPostParameters(["id", "name", "description"]);

  if (count($failedparams) == 0) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $active = "false";

    if (isset($_POST["active"]) && $_POST["active"] == "on") {
      $active = "true";
    }

    $project_dao = new project_dao();
    $project_dto = new project_dto();
    $project_dto = $project_dto->newProject($id, $name, $description, $active);

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