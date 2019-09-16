<?php
/**
 * Page for creating new tasks
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
 
 $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $task_id = filter_input(INPUT_GET, "id");
  if (!isset($task_id) || $task_id==null) {
    header("Location: /admin/index.php#projects");
    die();
  }
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($task->project_id);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | New task for "<?= $project->name ?>"</title>
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
        <li class="active">New task</li> 
      </ul>
      <div class="page-header">
        <h1>Duplicate of task <?= $task_id ?> for project "<?= $project->name ?>" project</h1>
        <p>The task will be created for this project. To create a task for another project, create it using the "New task" button existing on that page.</p>
      </div>
      <form class="form-horizontal new-task-form" action="/tasks/task_save.php" role="form" method="post" data-toggle="validator">
        <input type="hidden" name="project" value="<?= $task->project_id ?>">

        <div class="form-group">
          <label for="corpus" class="control-label col-sm-2">Mode</label>
          <div class="col-sm-4">
            <select class="form-control" required="" name="mode" id="mode">
              <option value="VAL" <?= ($task->mode == "VAL") ? "selected" : "" ?>>Validation</option>
              <option value="ADE" <?= ($task->mode == "ADE") ? "selected" : "" ?>>Adequacy</option>
              <option value="FLU" <?= ($task->mode == "FLU") ? "selected" : "" ?>>Fluency</option>
              <option value="RAN" <?= ($task->mode == "RAN") ? "selected" : "" ?>>Ranking</option>
            </select>
          </div>
        </div>
        <div class="form-group" id="source_lang_group">
          <label for="source_lang" class="control-label col-sm-2">Source language</label>
          <div class="col-sm-4">
            <select class="form-control" required=""  name="source_lang" id="source_lang">
              <option value="-1">Choose one</option>
              <?php
                $language_dao = new language_dao();
                $langs = $language_dao->getLanguages();
                foreach ($langs as $lang) { ?>  

                  <option value="<?= $lang->langcode ?>" <?= ($task->source_lang == $lang->langcode) ? "selected" : "" ?>><?= $lang->langcode ?> - <?= $lang->langname ?></option>

              <?php 
                }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="target_lang" class="control-label col-sm-2">Target language</label>
          <div class="col-sm-4">
            <select class="form-control" required=""  name="target_lang" id="target_lang">
              <option value="-1">Choose one</option>
              <?php
                $language_dao = new language_dao();
                $langs = $language_dao->getLanguages();
                foreach ($langs as $lang) { ?>

                  <option value="<?= $lang->langcode ?>"  <?= ($task->target_lang == $lang->langcode) ? "selected" : "" ?>><?= $lang->langcode ?> - <?= $lang->langname ?></option>

              <?php 
                }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="assigned_user" class="control-label col-sm-2">Evaluator</label>
          <div class="col-sm-4">
            <select disabled class="form-control" name="assigned_user" id="assigned_user" data-assigned="<?= $task->assigned_user ?>">
              <!-- Now we fill this with JavaScript :) -->
            </select>
            <div id="helpUsers" class="help-block with-errors d-none">
              No users available for this language pair. Please <a href="/admin/index.php#users">click here</a> to invite new users.
            </div>
          </div>
        </div>
        <?php
        ?>
        <div class="form-group">
          <label for="corpus" class="control-label col-sm-2">Corpus</label>
          <div class="col-sm-4">
            <select disabled class="form-control" required=""  name="corpus" id="corpus" data-corpus="<?= $task->corpus_id ?>">
              <!-- Now we fill this with JavaScript :) -->
            </select>
            <div id="helpCorpus" required=""  class="help-block with-errors d-none">
              No corpora available for this language pair. Please <a href="/admin/index.php#corpora">click here</a> upload a corpus first.
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-5 text-right">
            <a href="/projects/project_manage.php?id=<?php echo $project->id; ?>" class="col-sm-offset-1 btn btn-info">Cancel</a>
            <button type="submit" class="col-sm-offset-1 btn btn-success">Save</button>
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

    <script>
      $(document).ready(function() {
        $(".new-task-form select[name$='_lang'], .new-task-form select[name='mode']").trigger('change');
      })
    </script>
  </body>
</html>