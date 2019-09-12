<?php
/**
 * Recapitulation page, that shows the status of a task
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/task_stats_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/sentence_dao.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "id");

if (isset($task_id)) {
  $task_dao = new task_dao();
  $user_dao = new user_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->mode == "RAN") {
    $project_dao = new project_dao();
    $project = $project_dao->getProjectById($task->project_id);
    $assigned_user =  $user_dao->getUserById($task->assigned_user);
    
    if ($task->assigned_user == $USER->id || $USER->id == $project->owner) {
      $sentence_task_dao = new sentence_task_dao();
      $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);
    } else {
      // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
      // Message: You don't have access to this evaluation / We couldn't find this task for you
      header("Location: /index.php");
      die();
    }
  } else {
    // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
    // Message: You don't have access to this evaluation / We couldn't find this task for you
    header("Location: /index.php");
    die();
  }
} else {
  // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
  // Message: You don't have access to this evaluation / We couldn't find this task for you
  header("Location: /index.php");
  die();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Recap of task #<?= $task->id ?> - <?= $project->name ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>

    <link rel="stylesheet" href="/css/stars.css" />
  </head>
  <body>
    <div class="container evaluation">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/index.php">Tasks</a></li>
        <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>"  title="Go to the first pending sentence">Evaluation of <?= $project->name ?></a></li>
        <li class="active">Recap of Task #<?= $task->id ?></li>
      </ul>

      <div class="page-header">
          <div class="row">
            <div class="col-sm-8">
              <span class="h1">Recap of Task #<?php echo $task->id ?></span>
            </div>
          </div>
      </div>

      <div class="row">
        <?php
        if ($task->status == "DONE") {
        ?>
        <div class="col-sm-6">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Finished task</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-6 col-xs-12">
                  <p>This task has been marked as <b>DONE</b> and cannot be modified.</p>
                  <p>Thank you!</p>
                  <?php
                  if ($USER->id == $project->owner) {
                  ?>
                  <div>
                    <p>Evaluator: <?php echo $assigned_user->name;?> </p>
                    <p>Creation date: <?php echo getFormattedDate($task->creation_date); ?> <br>
                    Assigned on: <?php echo getFormattedDate($task->assigned_date); ?>  <br>
                    Completion date: <?php echo getFormattedDate($task->completed_date); ?></p>
                    <a href="mailto: <?php echo $assigned_user->email;?>">
                    <br><span class="glyphicon glyphicon-envelope"></span> Contact evaluator</a>
                  </div>
                  <br>
                  <?php
                  }
                  ?>
                  <?php if ($USER->id == $task->assigned_user) { ?>
                  <div>
                    <a href="/sentences/evaluate.php?review=1&task_id=<?= $task_id ?>">
                      <span class="glyphicon glyphicon-info-sign"></span>
                      <span>Check evaluation <span class="sr-only"> of task <?= $task_id ?></span>
                    </a>
                  </div>
                  <?php } ?>
                  <div>
                    <a href="/tasks/download_summary.php?task_id=<?php echo $task_id ?>">
                      <span class="glyphicon glyphicon-download-alt"></span>
                      <span>Download summary (TSV)</span>
                    </a>
                  </div>
                  <div>
                    <a href="/tasks/download_sentences.php?task_id=<?php echo $task_id ?>">
                      <span class="glyphicon glyphicon-download-alt"></span>
                      <span>Download annotated sentences (TSV)</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
        } else { ?>
        <div class="col-sm-6 col-xs-12">
        <?php if ($USER->id != $task->assigned_user) {
        ?>
          <div class="panel panel-warning">
            <div class="panel-heading">
              <h3 class="panel-title">Task in progress</h3>
            </div>
            <div class="panel-body">
              <p>This task is still in progress.
              <p>Evaluator: <?php echo $assigned_user->name; ?></p>
              <p>Creation date: <?php echo getFormattedDate($task->creation_date); ?> <br>
              Assigned on: <?php echo getFormattedDate($task->assigned_date); ?>  <br>
              <p><a href="mailto:<?php echo $assigned_user->email; ?>"><span class="glyphicon glyphicon-envelope"></span> Contact evaluator</a></p>
            </div>
          </div>
        <?php
        } else {
          if ($task_stats_dto->array_type["P"] == 0) {
          ?>
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Congrats! You've arrived to the end of the evaluation task</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-6 text-center">
                  <p>
                    <a href="/sentences/evaluate.php?p=1&id=1&task_id=<?= $task_id ?>" class="btn btn-lg btn-primary" style="margin-top: 1em; margin-bottom: 1em;">Review task</a>
                  </p>
                  <div class="row" style="display: flex; justify-content: center;">
                    <div class="col-sm-8">
                      <p><b>If you are not sure of your evaluation</b></p>
                      <p>
                        When you finish reviewing the task, you will be able to mark it as done again
                      </p>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 text-center">
                  <p>
                    <a href="/tasks/task_close.php?task_id=<?= $task_id ?>" class="btn btn-lg btn-success" style="margin-top: 1em; margin-bottom: 1em;">Tag as done</a>
                  </p>
                  <div class="row" style="display: flex; justify-content: center;">
                    <div class="col-sm-8">
                      <p><b>If you don't have any more doubts</b></p>
                      <p>
                        The Project Manager will receive a notice. You will be able to access the task, but not to modify it
                      </p>
                    </div>
                  </div>
                </div>
              </div>
          </div>
          <?php
          } else {
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
          <?php
          }
        } 
        ?>
        </div>
      <?php } ?>

        <div class="col-sm-6 col-xs-12">
          <canvas id="horizontal-bar-chart" class="w-100 h-100"></canvas>
          <p class="small text-center best-system-content d-none">
            According to your evaluation, <strong class="best-system-name"></strong> is the
            best system of the evaluated ones.
          </p>
        </div>
      </div>
    </div>
  
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>

    <script>
      $(document).ready(function() {
        $.ajax({
          url: "/services/recap.php",
          type: "POST",
          data: {
            "task_id": $("#task_id").val(),
            "service": "stats_ran"
          },
          success: function(json) {
            let response = JSON.parse(json);
            let stats_raw = response.stats;
            let stats_array = Object.keys(stats_raw).map(i => { let x = []; x[i] = stats_raw[i]; return x; } );
            let stats_ordered = stats_array.sort((a, b) => (a[Object.keys(a)[0]] < b[Object.keys(b)[0]] ? -1 : a[Object.keys(a)[0]] > b[Object.keys(b)[0]] ? 1 : 0));

            let labels = [];
            for (let i in stats_ordered) labels.push(Object.keys(stats_ordered[i])[0]);
            $(".best-system-name").html(labels[0]);
            $(".best-system-content").removeClass("d-none");

            let data = [];
            for (let i = 0; i < stats_ordered.length; i++) data.push(stats_ordered.length - i);

            let chart = new Chart(document.querySelector('#horizontal-bar-chart').getContext('2d'), {
              type: 'bar',
              data: {
                labels: labels,
                datasets: [{
                  backgroundColor: ["#7fbc89"].concat(Array(data.length - 1).fill("rgba(0, 74, 122, .5)")),
                  data: data
                }]
              },
              options: {
                legend: {
                    display: false
                },
                scales: {
                  yAxes: [{
                    display: false,
                    ticks: {
                        beginAtZero: true
                    }
                  }]
                },
                tooltips: {
                    enabled: false
                }
              }
            });
          }
        })
      });
    </script>

    <input type=hidden id="task_id" value="<?= $task->id ?>" />
  </body>
</html>