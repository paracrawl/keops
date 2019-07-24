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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Your projects</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
    <script>
      var charts = [];
    </script>
  </head>
  <body data-spy="scroll" data-target="#sidenav">
    <div class="container">
    <?php require_once(TEMPLATES_PATH . "/header.php"); ?>

    <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#projects">Projects</a></li>
        <li class="active">Your projects</li> 
    </ul>
    <div class="page-header">
        <h1>Your projects</h1>
    </div>

    <div class="col-xs-12 col-md-9">  
            <?php
                $project_dao = new project_dao();
                $task_dao = new task_dao();
                $sentence_task_dao = new sentence_task_dao();

                $projects_id = $project_dao->getProjectsIdByOwner(getUserId());
                $projects = array();
                $tasks = array();

                $total = 0;
                $done = 0;

                foreach ($projects_id as $project_id) {
                    $project = $project_dao->getProjectById($project_id);
                    $projects[] = $project;

                    $project_tasks = $task_dao->getTasksByProject($project->id);
                    $tasks[$project->id] = array();

                    foreach ($project_tasks as $task) {
                        $total++;
                        $done += ($task->status == "DONE") ? 1 : 0;
                        $tasks[$project->id][] = $task;
                    }
                }
            ?>

            <div class="row">
                <?php
                    if (count($projects) == 0) {
                ?>
                    You don't own any project. If you like, you can <a href="/projects/project_new.php">create a new one</a>.
                <?php } else { ?>

                <div class="col-xs-12 col-md-12">
                    <div class="row">
                        <div class="col-md-12 h3">Total progress in tasks</div>
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
                    </div>
                </div>
        

            <?php
                }

                foreach ($projects as $project) {
            ?>

            <div class="col-xs-12 col-md-12" >
                <div class="page-header row vertical-align" id="project<?= $project->id ?>">
                    <div class="col-xs-10 col-md-10">
                        <h3>Project #<?= $project->id ?> — <?= $project->name ?></h3>
                    </div>

                    <div class="col-xs-2 col-md-2 text-right">
                        <a href="/tasks/task_new.php?p_id=<?= $project->id ?>" class="btn btn-link" title="Add task to this project"><span class="glyphicon glyphicon-plus"></span></a>
                    </div>
                </div>

                <div id="stats-accordion">

                <?php
                    if (count($tasks[$project->id]) == 0) { ?>
                        This project has no tasks, but you can <a href="/tasks/task_new.php?p_id=<?= $project->id ?>">add one</a>.
                    <?php } ?>

                    <?php
                    foreach ($tasks[$project->id] as $task) {
                        $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);      
                ?>

                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="<?php echo "#task-stats-" . $task->id ?>" aria-expanded="false" aria-controls="<?php echo "task-stats-" . $task->id ?>">
                                <span class="glyphicon glyphicon-collapse-down"></span>Task #<?php echo $task->id; ?>
                                <span class="card-label">
                                    <?php if ($task->status == "DONE") { ?>
                                        <span class="label label-success">Done</span>
                                    <?php } else { ?>
                                        <span class="label label-default">Pending</span>
                                    <?php } ?>
                                </span>
                            </button>
                        </div>
                        <div id="<?php echo "task-stats-" . $task->id ?>" class="collapse" data-parent="stats-accordion">
                            <div class="card-body">
                                <div class="container-evaluation col-md-12">
                                    <div class="col-md-8">
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
                                    <div class="col-md-4">
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
                                    <div class="col-md-12">
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
                                    </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        </div>
        </div>

        <div class="col-md-3 visible-md visible-lg" style="position: sticky; top: 70px;">
            <nav id="sidenav">
                <ul class="nav nav-pills nav-stacked">
                    <?php foreach ($projects_id as $project_id) { ?>
                        <li role="presentation"><a href="#project<?= $project_id ?>">Project #<?= $project_id ?></a></li>
                    <?php } ?>
                </ul>
            </nav>
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