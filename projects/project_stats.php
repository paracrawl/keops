<?php
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
<html>
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
      <div class="page-header">
        <h1><?= $project->name ?></h1>
      </div>

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
        <div class="col-md-10">
          <p><strong>Description:</strong> <?= $project->description ?></p>
        </div>
        <div class="col-md-2">
          <a href="/projects/project_edit.php?id=<?php echo $project->id;?>" type="submit" class="col-sm-offset-1 btn btn-primary pull-right" tabindex="5">Edit</a>
          <a href="/projects/project_remove.php?id=<?php echo $project->id;?>" type="submit" class="col-sm-offset-1 btn btn-danger pull-right" tabindex="5">Remove</a>
        </div>
      </div>
      <div id class="title-container row">
        <div class="col-md-10 vcenter">
        <h3>Project Tasks</h3>
        </div><div class="col-md-2 vcenter">
          <a href="/tasks/task_new.php?p_id=<?= $project->id ?>" class="btn btn-primary pull-right">New task</a>
        </div>
      </div>
      <div id="stats-accordion" >
        <?php
        foreach ($tasks as $task) {
          $sentence_task_dao = new sentence_task_dao();
          $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);
          ?>

          <div class="card">
            <div class="card-header">
              <button class="btn btn-link collapsed" data-toggle="collapse" data-target="<?php echo "#task-stats-" . $task->id ?>" aria-expanded="false" aria-controls="<?php echo "task-stats-" . $task->id ?>">
                <span class="glyphicon glyphicon-collapse-down"></span>Task #<?php echo $task->id; ?>
              </button>

            </div>
            <div id="<?php echo "task-stats-" . $task->id ?>" class="collapse" data-parent="stats-accordion">
              <div class="card-body">
                <div class="container-evaluation col-md-12">
                  <div class="col-md-3">
                    
                    <div class="table-responsive">
                      <table class="table table-striped table-bordered table-hover table-condensed">
                        <thead>
                          <tr>
                            <th>Type</th>
                            <th># of sentences</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $labels_chart_js = array();
                          $data_chart_js = array();
                          foreach (sentence_task_dto::$labels as $label) {
                            $labels_chart_js[] = '"' . $label['value'] . '"';
                            $data_chart_js[] = $task_stats_dto->array_type[$label['value']];
                            ?>
                            <tr>
                              <td><?= $label['label'] ?> [<?= $label['value'] ?>]</td>
                              <td class="text-right"><?= $task_stats_dto->array_type[$label['value']] ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>

                        <tfoot>
                          <tr>
                            <th>Total</th>
                            <th class="text-right"><?= $task_stats_dto->total ?></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="col-md-6">

                    <canvas class="pie-charts" id="pie-chart-<?php echo $task->id; ?>" width="800" height="500"></canvas>
                    <script type="text/javascript">
                      var labels_pie_chart = [<?= implode(",", $labels_chart_js) ?>];
                      var data_pie_chart = [<?= implode(",", $data_chart_js) ?>];
                      chart = [];
                      chart["id"] = "pie-chart-<?php echo $task->id; ?>";
                      chart["labels"] = labels_pie_chart;
                      chart["data"] = data_pie_chart;
                      charts.push(chart);
                    </script>
                  </div>
                  <div class="col-md-3">
                                          <?php
                    if ($task->status == "DONE") {
                      ?>
                      <div class="panel panel-success">
                        <div class="panel-heading">
                          <h3 class="panel-title">Finished task</h3>
                        </div>
                       <div class="panel-body">
                         <p>Evaluator: <?php echo $task->username; ?><p>
                             <p>Creation date: <?php echo getFormattedDate($task->creation_date); ?> <br>
                               Assigned on: <?php echo getFormattedDate($task->assigned_date); ?>  <br>
                               Completion date: <?php echo getFormattedDate($task->completed_date); ?></p>
                             </p>
                             <a href="mailto:<?php echo $task->email; ?>">
                               <br><span class="glyphicon glyphicon-envelope"></span> Contact evaluator</a>
                             <br>
                             <div>
                               <a href="/tasks/download_summary.php?task_id=<?php echo $task->id ?>">
                                 <span class="glyphicon glyphicon-download-alt"></span>
                                 <span>Download summary (CSV)</span>
                               </a>
                             </div>
                             <div>
                               <a href="/tasks/download_sentences.php?task_id=<?php echo $task->id ?>">
                                 <span class="glyphicon glyphicon-download-alt"></span>
                                 <span>Download annotated sentences (TSV)</span>
                               </a>
                             </div>
                           </div>
                      </div>
                      <?php
                    } else {
                      ?>
                      <div class="panel panel-warning">
                        <div class="panel-heading">
                          <h3 class="panel-title">Task in progress</h3>
                        </div>
                        <div class="panel-body">
                          <p>This task is still in progress.
                          <p>Evaluator: <?php echo $task->username; ?><p>
                          <p>Creation date: <?php echo getFormattedDate($task->creation_date); ?> <br>
                            Assigned on: <?php echo getFormattedDate($task->assigned_date); ?>  <br>
          <!--                  Completion date: <?php echo getFormattedDate($task->completed_date); ?></p>-->
                          </p>
                          <a href="mailto:<?php echo $task->email; ?>">
                            <br><span class="glyphicon glyphicon-envelope"></span> Contact evaluator</a>
                        </div>
                      </div>
                      <?php
                    }?>
                </div>
              </div>
            </div>
          </div>
          </div>
          <?php
        }
        ?>
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