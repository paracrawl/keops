<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | New task</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>New task</h1>
      </div>
      <form class="form-horizontal" action="/task_save.php" role="form" method="post" data-toggle="validator">
        <?php
        $project_dao = new project_dao();
        $projects = $project_dao->getProjects();
        $project_selected = filter_input(INPUT_GET, "p_id");
        ?>
        <div class="form-group">
          <label for="project" class="control-label col-sm-1">Project</label>
          <div class="col-sm-4">
            <select class="form-control" name="project" id="project" tabindex="1">
              <?php foreach ($projects as $p) { ?>
                <option value="<?= $p->id?>"<?= $project_selected == $p->id ? " selected" : "" ?>><?= $p->name ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <?php
        $user_dao = new user_dao();
        $users = $user_dao->getUsers();
        ?>
        <div class="form-group">
          <label for="assigned_user" class="control-label col-sm-1">Evaluator</label>
          <div class="col-sm-4">
            <select class="form-control" name="assigned_user" id="assigned_user" tabindex="2">
              <?php foreach ($users as $user) { ?>
                <option value="<?= $user->id?>"><?= $user->name ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <?php
        $corpus_dao = new corpus_dao();
        $corpus = $corpus_dao->getCorpora();
        ?>
        <div class="form-group">
          <label for="assigned_user" class="control-label col-sm-1">Evaluator</label>
          <div class="col-sm-4">
            <select class="form-control" name="assigned_user" id="assigned_user" tabindex="2">
              <?php foreach ($users as $user) { ?>
                <option value="<?= $user->id?>"><?= $user->name ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/#projects" class="col-sm-offset-1 btn btn-danger" tabindex="6">Cancel</a>
            <button type="submit" class="col-sm-offset-1 btn btn-success" tabindex="5">Save</button>
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