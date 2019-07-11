<?php
/**
 * Page to remove a project.
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $project_id = filter_input(INPUT_GET, "id");
  if (!isset($project_id)) {
    header("Location: /admin/index.php#projects");
    die();
  }
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($project_id);
//  $task_dao  = new task_dao();
// $tasks = $task_dao->getTasksByCorpus($corpus_id);
//  $language_dao = new language_dao();
//  $languages = $language_dao->getLanguages();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Remove project</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#projects">Projects</a></li>
        <li><a href="/projects/project_manage.php?id=<?php echo $project->id; ?>"><?= $project->name?></a></li>
        <li class="active">Remove project</li> 
      </ul>
      <div class="page-header">
        <h1>Remove project</h1>
      </div>

      <div class="alert alert-warning" role="alert">
        <p class="h3" style="margin-top: 0px;">
          Removing <?= $project->name ?> will result in the removal of the tasks listed below.
        </p>

        <p>
        <b>Tip:</b>
          If you just want to prevent the creation of new tasks for this project, not removing the already existing ones, mark it as inactive <a href="/projects/project_edit.php?id=<?=$project->id; ?>" class="alert-link">here</a>.
        </p>
        <!--<h3>BEWARE!</h3>
        <br>
        Removing the project <b><?=$project->name?></b> will result in the removal of the tasks listed below. Are sure you want to proceed? Please note that this operation <b>cannot be undone</b>. <br>         
        <br>
        <b>Tip:</b>
        If you just want to prevent the creation of new tasks for this project, not removing the already existing ones, mark it as inactive <a href="/projects/project_edit.php?id=<?=$project->id; ?>">here</a>.
        -->
      </div>

    <table id="tasks-table" class="table table-striped table-bordered display responsive nowrap" data-projectid="<?= $project->id ?>" cellspacing="0" style="width:100%;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Assigned user</th>
            <th>Size</th>
            <th>Corpus</th>  
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
            <th>Corpus</th>  
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>

      <form action="/projects/project_update.php" role="form" method="post">
        <input type="hidden" name="id" id="id" value="<?= $project->id ?>">
        <input type="hidden" name="action" id="action" value="remove">

        <!--
        <div class="form-group">
          <div class="col-sm-12 text-center">
            <a href="/projects/project_manage.php?id=<?php echo $project->id;?>" class="btn btn-info" tabindex="6">Cancel</a>
            <button type="submit" class="btn btn-danger" tabindex="5">Remove project</button>
          </div>
        </div>-->

        <div class="row" style="margin-top: 1em;">
          <div class="col-xs-6 col-md-6">
            <a href="/projects/project_manage.php?id=<?php echo $project->id;?>" class="btn btn-default" tabindex="6">Cancel</a>
          </div>  
          <div class="col-xs-6 col-md-6 text-right">
          <a href="/projects/project_edit.php?id=<?=$project->id; ?>" class="btn btn-info" tabindex="6" title="Keeps the project but prevents the creation of new tasks">Mark as inactive</a>
            <button type="submit" class="btn btn-danger" tabindex="5">Remove project</button>
          </div>
        </div>
      </form>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    require_once(TEMPLATES_PATH . "/modals.php")

    ?>

    <input type="hidden" id="input_isowner" value="<?php echo (getUserId() == $project->owner); ?>" />
  </body>
</html>