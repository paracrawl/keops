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
$search_term = filter_input(INPUT_GET, "term"); // Query
$label = filter_input(INPUT_GET, "label"); // Any sentence ("ALL" label) or those labeled as MT, V, P and so on
$page = filter_input(INPUT_GET, "page"); // Page of the results we want to show.

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
            <li class="active">Search in Task #<?= $task->id ?></li> 
            <a class="pull-right"  href="mailto:<?= $project->owner_object->email  ?>" title="Contact Project Manager">Contact PM <span id="contact-mail-logo" class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
        </ul>

        <div class="col-md-12" style="margin-bottom: 1em;">
            <div class="page-header" style="padding-bottom: 0px;">
                <div class="row">
                    <div class="col-sm-6" style="margin-bottom: 1em;">
                        <span class="h1">Search in Task #<?php echo $task->id ?></span>
                    </div>

                    <div class="col-sm-6 text-right">
                        <?php require_once(TEMPLATES_PATH . "/sentences_search.php"); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php
                        $page = (isset($page)) ? $page : 1;
                        $sentence_task_dao = new sentence_task_dao();
                        $result = $sentence_task_dao->getSentencesIdByTermAndTaskAndLabel($task->id, $search_term, $label, $page);  
                        $result_ids = $result["sentence_ids"];

                        if (count($result_ids) == 0) {
                            // No results
                            ?> No results found for that query <?php
                        } else {
                            for ($i = 0; $i < SENTENCES_SEARCH_MAX && $i < count($result_ids); $i++) {
                                $result_id = $result_ids[$i];
                                $from = $result_id;
                                $sentence = $sentence_task_dao->getSentenceByIdAndTask($result_id, $task->id);
                    ?>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="row panel-title" style="display: flex;align-items: baseline;">
                                    <div class="col-xs-6">
                                        Sentence #<?php echo $sentence->id; ?>
                                    </div>
                                    <div class="col-xs-6 text-right" style="display:flex; justify-content: flex-end; align-items: baseline;">
                                        <span style="margin-right: 0.5em;">Labeled as <strong><?php echo $sentence->evaluation; ?></strong></span>
                                        <a class="btn btn-default" href="/sentences/evaluate.php?task_id=<?php echo $task->id; ?>&id=<?php echo $sentence->id; ?>">Evaluate</a>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <!-- TODO: Term in bold -->
                                <?php 
                                    echo preg_replace('/(^|.*[,|.| |"]+)(' . $search_term . ')([,|.| |]+|$)/i', "$1<strong>$2</strong>$3", $sentence->source_text);
                                ?>
                                <hr />
                                <?php 
                                    echo preg_replace('/(^|.*[,|.| |"]+)(' . $search_term . ')([,|.| |]+|$)/i', "$1<strong>$2</strong>$3", $sentence->target_text);
                                ?>
                            </div>
                        </div>
                    <?php
                        }
                    ?>

                    <div class="row text-center">
                        <div class="col-sm-offset-4 col-sm-4">
                            <ul class="pagination pagination-sm">
                                <?php
                                    if ($page != 1) { ?>
                                        <li><a href="/sentences/search.php?task_id=<?php echo $task->id; ?>&term=<?php echo $search_term; ?>&label=<?php echo $label; ?>&page=<?php echo $page-1; ?>">Prev</a></li>
                                    <?php
                                    } else { 
                                    ?>
                                        <li class="disabled"><a href="#">Prev</a></li>
                                    <?php 
                                    } 
                                    ?>

                                    <li class="active"><a href="#"><?= $page ?>/<?= $result["pages"] ?></a></li>
                                    
                                    <?php
                                    if ($page != $result["pages"]) { ?>
                                        <li><a href="/sentences/search.php?task_id=<?php echo $task->id; ?>&term=<?php echo $search_term; ?>&label=<?php echo $label; ?>&page=<?php echo $page+1; ?>">Next</a></li>
                                    <?php
                                    } else { 
                                    ?>
                                        <li class="disabled"><a href="#">Next</a></li>
                                    <?php 
                                    } 
                                    ?>
                            </ul>
                        </div>

                        <div class="col-sm-4">
                            <div class="row">
                                    <div class="col-sm-offset-6 col-sm-6 text-right">
                                        <form id="gotoform" method="get" action="/sentences/search.php">
                                            <input type="hidden" name="task_id" value="<?= $task->id ?>">
                                            <input type="hidden" name="term" value="<?= $search_term ?>">
                                            <input type="hidden" name="label" value="<?= $label ?>">
                                            <input class="form-control go-to-page" id="gotopage" name="page" type="number" min="1" max="<?= $result["pages"] ?>" value="<?= $page ?>">
                                            <button type="submit" class="btn btn-xs btn-link">Go!</button>
                                        </form>
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