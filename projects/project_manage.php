<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $project_id = filter_input(INPUT_GET, "id");
  if (!isset($project_id)) {
    header("Location: /admin/#projects");
    die();
  }
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($project_id);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Project: <?= $project->name ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1><?= $project->name ?></h1>
      </div>
      Tasks
      New task
      
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>