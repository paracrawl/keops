<?php
/**
 * Evaluation page for a sentence
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

  
$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");
$search_term = filter_input(INPUT_GET, "term");

if (isset($task_id) && isset($search_term)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->assigned_user == $USER->id) {
    $project_dao = new project_dao();
    $project = $project_dao->getProjectById($task->project_id);
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
    <title>KEOPS | Search in task #<?= $task->id ?> - <?= $project->name ?></title>
    <?php
        require_once(TEMPLATES_PATH . "/head.php");
    ?>
</head>
<body>
    <div id="evaluation-container" class="container evaluation">
        <?php require_once(TEMPLATES_PATH . "/header.php"); ?>

        <?php if ($task->status == "DONE") { ?>
            <div class="alert alert-success" role="alert">
                <b>This task is done!</b> The evaluation can be read but not changed.
            </div>
        <?php } ?>

        <ul class="breadcrumb">
            <li><a href="/index.php">Tasks</a></li>
            <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>" title="Go to the first pending sentence">Evaluation of <?= $project->name ?> </a></li>
            <li class="active">Task #<?= $task->id ?></li> 
            <a class="pull-right"  href="mailto:<?= $project->owner_object->email  ?>" title="Contact Project Manager">Contact PM <span id="contact-mail-logo" class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
        </ul>

        <div class="col-md-12" style="margin-bottom: 1em;">
            <div class="page-header" style="padding-bottom: 0px;">
                <div class="row">
                    <div class="col-sm-4" style="margin-bottom: 1em;">
                        <span class="h1">Task #<?php echo $task->id ?></span>
                    </div>

                    <div class="col-sm-8 text-right">
                        <form action="" class="form-inline">
                            <div class="form-group">
                                <input type="hidden" id="task_id" name="task_id" value="<?= $task->id ?>">
                                <input class="form-control" id="search-term" name="term" value="<?php echo $search_term; ?>" placeholder="Search through sentences">

                                <select class="form-control">
                                    <option>Everything</option>
                                    <option>L annotation</option>
                                    <option>A annotation</option>
                                    <option>T annotation</option>
                                    <option>MT annotation</option>
                                    <option>F annotation</option>
                                </select>

                                <button type=submit class="btn btn-primary" id="search-term-button">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php
                        $sentence_task_dao = new sentence_task_dao();
                        $result_ids = $sentence_task_dao->getSentencesIdByTermAndTask($task->id, $search_term);  
                        foreach($result_ids as $result_id) {
                            $sentence = $sentence_task_dao->getSentenceByIdAndTask($result_id, $task->id);
                    ?>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row panel-title">
                                    <div class="col-sm-10">
                                        Sentence #<?php echo $sentence->id; ?>
                                    </div>
                                    <div class="col-sm-2 text-right">
                                        <a href="/sentences/evaluate.php?task_id=<?php echo $task->id; ?>&id=<?php echo $sentence->id; ?>">Go</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <!-- TODO: Term in bold -->
                                <?php echo str_replace($search_term, "<b>".$search_term."</b>", $sentence->source_text); ?>
                                <hr />
                                <?php echo str_replace($search_term, "<b>".$search_term."</b>", $sentence->source_text); ?>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>