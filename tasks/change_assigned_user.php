<?php
/**
 * Page for creating new tasks
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_langs_dao.php");
 
 $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $task_id = filter_input(INPUT_GET, "task_id");
  if (!isset($task_id) || $task_id==null) {
    //header("Location: /admin/index.php#projects");
    throw Exception("Task not set or not found");
    die();
  }
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);

  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($task->project_id);

  if ($project->owner != getUserId()) {
    // The evaluator of a task can only be reassigned by the Project Manager
    // of the project the task belongs to
    header("Location: /projects/project_manage.php?id=" . $task->project_id);
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Change assigned user for Task #<?= $task->id ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/index.php">Tasks</a></li>
        <li class="active">Change assigned user for Task #<?= $task->id ?></li>
      </ul>
      <div class="page-header">
        <h1>Change assigned user for Task #<?= $task->id ?></h1>
        <p>
            This task belongs to <b>Project #<?= $project->id ?> (<?= $project->description ?>, <?= $project->source_lang_object->langcode ?>-<?= $project->target_lang_object->langcode ?>)</b>
        </p>
      </div>
      <form action="/tasks/task_update.php" class="row" role="form" method="post" data-toggle="validator">
        <input type="hidden" name="id" value="<?= $task->id ?>">
        <input type="hidden" name="project_id" value="<?= $task->project_id ?>">
        <input type="hidden" name="action" value="reassign">

        <?php
        $user_dao = new user_dao();
        $assigned_user = $user_dao->getUserById($task->assigned_user);

        $user_langs_dao = new user_langs_dao();
        $user_ids= $user_langs_dao->getUserIdsByLangPair($project->source_lang, $project->target_lang);
        
        if (!empty($user_ids)) {
          $users = $user_dao->getUsersByIds($user_ids);
        }
        else {
          $users = array();
        }

        ?>

        <div class="col-sm-6">
            <div class="form-group row">
                <div class="col-sm-4 col-form-label col-form-label-sm">
                    <label for="assigned_user">New evaluator</label>
                </div>
                <div class="col-sm-8">
                    <select class="form-control" name="assigned_user" id="assigned_user" tabindex="2">
                    <option value="<?= $assigned_user->id?>"><?= $assigned_user->name ?></option>
                    <?php foreach ($users as $user) { if ($user->id != $task->assigned_user) { ?>
                        <option value="<?= $user->id?>"><?= $user->name ?></option>
                    <?php }} ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-offset-4 col-sm-8 mt-2 text-right">
                    <hr />
                    <a href="/projects/project_manage.php?id=<?php echo $task->project_id; ?>" class="btn btn-info" tabindex="6">Cancel</a>
                    <button type="submit" class="btn btn-success" tabindex="5">Save</button>
                </div>
            </div>
        </div>
      </form>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>