<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
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
      <ul class="breadcrumb">
        <li><a href="/admin">Management</a></li>
        <li><a href="/admin/#projects">Projects</a></li>
        <li><a href="/projects/project_manage.php?id=<?php echo $project->id; ?>"><?= $project->name?></a></li>
        <li class="active">Manage project</li> 
      </ul>
      <div class="page-header">
        <h1><?= $project->name ?></h1>
      </div>
                <?php
        if (isset($_SESSION["error"])) {
        ?>
        <div class="panel panel-danger">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Oops!</strong></h3>
          </div>
          <div class="panel-body">
            <p>
            <?php
              switch ($_SESSION["error"]) {         
                case "errorcreatingtask":
                default:
                  echo "Sorry, your request could not be processed. Please, try again later.";
                  break;
              }
              $_SESSION["error"] = null;
            ?>
            </p>
          </div>
        </div>
        <?php
        }
        ?>
      <div class="row">
        <div class="col-md-3">
          <p><strong>Language pair:</strong> <?= $project->source_lang_object->langcode ?>-<?= $project->target_lang_object->langcode ?></p>
        </div>
        <div class="col-md-3">
          <p><strong>Owner:</strong> <a href="/admin/user_edit.php?id=<?= $project->owner ?>"><?= $project->owner_object->name ?></a></p>
        </div>
        <div class="col-md-3">
          <p><strong>Creation date:</strong> <?= getFormattedDate($project->creation_date) ?></p>
        </div>
        <div class="col-md-3">
          <?php $icon_class = $project->active ? "ok green" : "remove red"; ?>
          <p><strong>Active?</strong> <span class="glyphicon glyphicon-<?= $icon_class ?>" aria-hidden="true"></span></p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <p><strong>Description:</strong> <?= $project->description ?></p>
        </div>
      </div>
      <div class="title-container">
        <h3 class="inline">Project Tasks</h3>
        <p class="float-right inline">
          <a href="/tasks/task_new.php?p_id=<?= $project->id ?>" class="btn btn-primary">New task</a>
        </p>
      </div>
      <hr>
      <table id="tasks-table" class="table table-striped table-bordered" data-projectid="<?= $project->id ?>" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Assigned user</th>
            <th>Size</th>
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th>Assigned user</th>
            <th>Size</th>
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>