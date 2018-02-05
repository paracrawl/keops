<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | New project</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>New project</h1>
      </div>
      <form class="form-horizontal" action="/services/project_service.php?service=new" role="form" method="post" data-toggle="validator">
        <div class="form-group">
          <label for="name" class="col-sm-1 control-label">Name</label>
          <div class="col-sm-4">
            <input type="text" name="name" class="form-control" aria-describedby="helpName" placeholder="Name" maxlength="100" required="" autofocus="">
            <div id="helpName" class="help-block with-errors"></div>
          </div>
        </div>
        <?php
        $language_dao = new language_dao();
        $languages = $language_dao->getLanguages();
        ?>
        <div class="form-group">
          <label for="source_lang" class="control-label col-sm-1">Source language</label>
          <div class="col-sm-4">
            <select class="form-control" name="source_lang" id="source_lang">
              <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="target_lang" class="control-label col-sm-1">Target language</label>
          <div class="col-sm-4">
            <select class="form-control" name="target_lang" id="target_lang">
              <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="description" class="col-sm-1 control-label">Description</label>
          <div class="col-sm-4">
            <textarea name="description" class="form-control" aria-describedby="helpDesc" placeholder="Description" maxlength="500" required=""></textarea>
            <div id="helpDesc" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="page-header">
          <h3>Adding tasks</h3>
        </div>
        <div class="form-group">
          <label for="task_size" class="col-sm-1 control-label">Task size</label>
          <div class="col-sm-4">
            <input type="number" max="10000" min="0" value="2000" name="task_size" class="form-control" aria-describedby="helpTaskSize" required="">
            <div id="helpTaskSize" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/#projects" class="col-sm-offset-1 btn btn-danger">Cancel</a>
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
  </body>
</html>