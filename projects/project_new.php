<?php
/**
 * Page to create a new project
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
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
        <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#projects">Projects</a></li>
        <li class="active">New project</li> 
      </ul>
      <div class="page-header">
        <h1>New project</h1>
      </div>
      <form class="form-horizontal" action="/services/project_service.php?service=new" role="form" method="post" data-toggle="validator">
        <div class="form-group">
          <label for="name" class="col-sm-1 control-label">Name</label>
          <div class="col-sm-4">
            <input type="text" name="name" class="form-control" aria-describedby="helpName" placeholder="Name" maxlength="100" required="" autofocus="" tabindex="1">
            <div id="helpName" class="help-block with-errors">Write the public name of the project</div>
          </div>
        </div>
        <?php
        $language_dao = new language_dao();
        $languages = $language_dao->getLanguages();
        ?>
        <div class="form-group">
          <label for="source_lang" class="control-label col-sm-1">Source language</label>
          <div class="col-sm-4">
            <select class="form-control" name="source_lang" id="source_lang" tabindex="2">
              <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="target_lang" class="control-label col-sm-1">Target language</label>
          <div class="col-sm-4">
            <select class="form-control" name="target_lang" id="target_lang" tabindex="3">
              <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="description" class="col-sm-1 control-label">Description</label>
          <div class="col-sm-4">
            <textarea name="description" class="form-control" aria-describedby="helpDesc" placeholder="Description" maxlength="500" required="" tabindex="4"></textarea>
            <div id="helpDesc" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/index.php#projects" class="col-sm-offset-1 btn btn-info" tabindex="6">Cancel</a>
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