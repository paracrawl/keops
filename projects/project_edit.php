<?php
/**
 * Page for editing the metadata of a project
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Edit project</title>
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
        
        <li class="active">Edit project</li> 
      </ul>
      <div class="page-header">
        <h1>Edit project</h1>
      </div>
      <form class="form-horizontal" action="/projects/project_update.php" role="form" method="post" data-toggle="validator">
        <div class="form-group">
          <label for="id" class="col-sm-1 control-label">ID</label>
          <div class="col-sm-4">
            <input type="text" disabled class="form-control" aria-describedby="helpName" placeholder="ID" maxlength="100" required="" autofocus="" value="<?= $project->id ?>">
            <div id="helpName" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <label for="name" class="col-sm-1 control-label">Name</label>
          <div class="col-sm-4">
            <input type="text" name="name" class="form-control" aria-describedby="helpName" placeholder="Name" maxlength="100" required="" autofocus="" tabindex="1" value="<?= $project->name ?>">
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
            <select class="form-control" name="source_lang" id="source_lang" tabindex="2">
              <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id?>"<?= $project->source_lang == $lang->id ? " selected" : "" ?>><?= $lang->langcode . " - " . $lang->langname ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="target_lang" class="control-label col-sm-1">Target language</label>
          <div class="col-sm-4">
            <select class="form-control" name="target_lang" id="target_lang" tabindex="3">
              <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id?>"<?= $project->target_lang == $lang->id ? " selected" : "" ?>><?= $lang->langcode . " - " . $lang->langname ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="description" class="col-sm-1 control-label">Description</label>
          <div class="col-sm-4">
            <textarea name="description" class="form-control" aria-describedby="helpDesc" placeholder="Description" maxlength="500" required="" tabindex="4"><?= $project->description ?></textarea>
            <div id="helpDesc" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <label for="creation_date" class="col-sm-1 control-label">Creation date</label>
          <div class="col-sm-4">
            <input id="creation_date" type="text" disabled class="form-control" aria-describedby="helpName" placeholder="Creation date" maxlength="100" required="" autofocus="" value="<?= getFormattedDate($project->creation_date) ?>">
            <div id="helpName" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <label for="active" class="col-sm-1 control-label">Active?</label>
          <div class="col-sm-4">
            <input type="checkbox" name="active"<?= $project->active ? " checked" : "" ?>>
            <div id="helpName" class="help-block with-errors">If you uncheck this project, it will not be available for creating new tasks. </div>
          </div>
        </div>
        <input type="hidden" name="id" id="id" value="<?= $project->id ?>">
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/projects/project_manage.php?id=<?=$project->id?>" class="col-sm-offset-1 btn btn-info" tabindex="6">Cancel</a>
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