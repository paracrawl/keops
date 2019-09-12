<?php
/**
 * Page to display the stats of a given project
 */
// load up your config file
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/sentence_task_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$project_id = filter_input(INPUT_GET, "id");
if (!isset($project_id)) {
  header("Location: /admin/index.php#projects");
  die();
}
$project_dao = new project_dao();
$project = $project_dao->getProjectById($project_id);
$task_dao = new task_dao();
$tasks = $task_dao->getTasksByProject($project_id);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Project: <?= $project->name ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
    <script>
      var charts = [];
    </script>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#projects">Projects</a></li>
        <li><a href="/projects/project_manage.php?id=<?php echo $project->id; ?>"><?= $project->name ?></a></li>
        <li class="active">Project stats</li> 
      </ul>

      <div class="page-header row vertical-align">
        <div class="col-xs-8 col-md-6">
          <h1><?= $project->name ?></h1>
        </div>
        <div class="col-xs-4 col-md-6 text-right">
          <a href="/projects/project_edit.php?id=<?php echo $project->id;?>" type="submit" class="btn btn-link mr-4" title="Edit project">
              <span class="glyphicon glyphicon-edit"></span>
              <span>Edit</span>
          </a>
          
          <a href="/projects/project_remove.php?id=<?php echo $project->id;?>" type="submit" class="btn btn-link" title="Remove project">
              <span class="text-danger">
                <span class="glyphicon glyphicon-trash"></span>
                <span>Remove</span>
              </span>
            </a>
        </div>
      </div>

      <div class="row">
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

      <div class="row">
        <?php
          $total = 0;
          $done = 0;

          foreach ($tasks as $task) {
              $total++;
              $done += ($task->status == "DONE") ? 1 : 0;
          }
        ?>

        <div class="col-md-12 h3">
          Total progress in tasks
        </div>

        <div class="col-xs-6 col-md-6">
        <?= $done ?> done
        </div>

        <div class="col-xs-6 col-md-6 text-right">
          <?= $total ?> total
        </div>

        <div class="col-xs-12 col-md-12">
          <div class="progress">
              <div class="progress-bar progress-bar-success" style="width: <?php echo ($done / $total) * 100 ?>%">
                  <?php echo floor(($done / $total) * 100) ?>%<span class="sr-only"> tasks done</span>
              </div>
          </div>
        </div>

        <div class="col-sm-12 col-xs-12">
          <ul class="list-group list-group-columns">
            <?php
            $sentence_task_dao = new sentence_task_dao();
            foreach ($tasks as $task) {
                $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);      
            ?>

            <a href="/tasks/recap.php?id=<?= $task->id ?>" class="list-group-item col-sm-4">
                Task #<?php echo $task->id; ?>
                <div class="float-right">
                    <?php if ($task->status == "DONE") { ?>
                        <span class="label label-success p-2" style="display: inline-block; width: 70px;">Done</span>
                    <?php } else { ?>
                        <span class="label label-default p-2" style="display: inline-block; width: 70px;">Pending</span>
                    <?php } ?>
                </div>
            </a>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>

</html>