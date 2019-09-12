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
<html lang="en">
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

    <div class="row">
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
                        $done = 0;
                        $total = count($tasks[$project->id]);
                        foreach($tasks[$project->id] as $task) {
                            $done += ($task->status == "DONE") ? 1 : 0;
                        }
                ?>

                <div class="col-xs-12 col-md-12" >
                    <div class="page-header row vertical-align-sm">
                        <div class="col-xs-12 col-md-4 col-md-push-8 text-right-sm">
                            <span class="label label-default"><?= ($total - $done) ?> pending</span>
                            <span class="label label-success mr-2"><?= $done ?> done</span>
                        </div>

                        <div class="col-xs-12 col-md-8 col-md-pull-4">
                            <div id="project<?= $project->id ?>" style="position: relative; top: -60px;"></div>
                            <h3>Project #<?= $project->id ?> — <?= $project->name ?></h3>
                        </div>
                    </div>

                    <ul class="list-group list-group-columns">

                    <?php
                        if (count($tasks[$project->id]) == 0) { ?>
                            This project has no tasks, but you can <a href="/tasks/task_new.php?p_id=<?= $project->id ?>">add one</a>.
                        <?php } ?>

                        <?php
                        foreach ($tasks[$project->id] as $task) {
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
                <?php } ?>
            </div>
        </div>

        <div class="col-md-3 visible-md visible-lg" style="position: sticky; top: 70px;">
            <nav id="sidenav">
                <ul class="nav nav-pills nav-stacked" style="height: calc(100vh - 80px); overflow-y: scroll;">
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