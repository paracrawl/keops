<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_stats_dto.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "id");

if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->assigned_user == $USER->id) {
    $project_dao = new project_dao();
    $project = $project_dao->getProjectById($task->project_id);
    
    $sentence_task_dao = new sentence_task_dao();
    $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);
  }
  else {
    // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
    // Message: You don't have access to this evaluation / We couldn't find this task for you
    header("Location: /index.php");
    die();
  }
}
else {
  // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
  // Message: You don't have access to this evaluation / We couldn't find this task for you
  header("Location: /index.php");
  die();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Recap of task #<?= $task->id ?> - <?= $project->name ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container evaluation">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/index.php">Tasks</a></li>
        <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>">Evaluation of <?= $project->name ?></a></li>
        <li class="active">Recap Task #<?= $task->id ?></li>
                <a style="float: right"  href="mailto:<?= $project->owner_object->email  ?>"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>

      </ul>
      <!--<div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover table-condensed">
            <thead>
              <tr>
                <th>Total</th>
              <?php foreach (sentence_task_dto::$labels as $label) { ?>
                <th title="<?= $label['label'] ?>"><?= $label['value'] ?></th>
              <?php } ?>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= $task_stats_dto->total ?></td>
              <?php foreach (sentence_task_dto::$labels as $label) { ?>
                <td title="<?= $label['label'] ?>"><?= $task_stats_dto->array_type[$label['value']] ?></td>
              <?php } ?>
              </tr>
            </tbody>
          </table>
        </div>
      </div>-->
      <div class="col-md-4">
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover table-condensed">
            <thead>
              <tr>
                <th>Type</th>
                <th># of sentences</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Total</td>
                <td><?= $task_stats_dto->total ?></td>
              </tr>
              <?php
              $labels_chart_js = array();
              $data_chart_js = array();
              foreach (sentence_task_dto::$labels as $label) {
                $labels_chart_js[] = '"' . $label['value'] . '"';
                $data_chart_js[] = $task_stats_dto->array_type[$label['value']];
                ?>
              <tr>
                <td><?= $label['label'] ?> [<?= $label['value'] ?>]</td>
                <td><?= $task_stats_dto->array_type[$label['value']] ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-md-8">
        <canvas id="pie-chart" width="800" height="350"></canvas>
        <script type="text/javascript">
          var labels_pie_chart = [<?= implode(",", $labels_chart_js) ?>];
          var data_pie_chart = [<?= implode(",", $data_chart_js) ?>];
        </script>
      </div>
      <div class="col-md-12">
        <?php
        if ($task->status == "DONE") {
          ?>
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Finished task</h3>
            </div>
            <div class="panel-body">
              <p>This task has been marked as <b>DONE</b> and cannot be modified.</p>
              <p>Thank you!</p>
            </div>
          </div>
          <?php
        }
      else {
        if ($task_stats_dto->array_type["P"] == 0) {
          ?>
            <div class="panel panel-success">
              <div class="panel-heading">
                <h3 class="panel-title">Congrats! You've arrived to the end of the evaluation task</h3>
              </div>
              <div class="panel-body">
                <p>If you are sure that you finished the task and you don't have more doubts, please mark the task as DONE using the following button.</p>
                <p class="text-center"><a href="/tasks/task_close.php?task_id=<?= $task_id ?>" class="btn btn-lg btn-success">Mark as DONE</a></p>
                <p>If you mark the task as DONE, you will not have access to the task afterwards and the Project Manager  will receive a notice.</p>
                <p>Thank you!</p>
              </div>
            </div>
        <?php } else {
          ?>
            <div class="panel panel-warning">
              <div class="panel-heading">
                <h3 class="panel-title">Oops! You didn't finish the task</h3>
              </div>
              <div class="panel-body">
                <p>It seems that there are sentences that are not evaluated yet. Please come back when you finish and you will be able to mark the task as DONE.</p>
                <p>Thank you!</p>
                <p class="text-center"><a href="/sentences/evaluate.php?task_id=<?= $task_id ?>" class="btn btn-lg btn-warning">Continue task</a></p>
               
              </div>
            </div>
        <?php }
      }
      ?>
      </div>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>