<?php
/**
 * Page for project management
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/project_dao.php");

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
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Project: <?= $project->name ?></title>
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
        <li class="active">Manage project</li> 
      </ul>
      <div class="title-container">
        <div class="row vertical-align">
          <div class="col-xs-6 col-md-6">
            <h1 style="margin-top: 0;"><?= $project->name ?></h1>
          </div>

          <div class="col-xs-6 col-md-6 text-right">
            <a href="/projects/project_edit.php?id=<?php echo $project->id;?>" type="submit" title="Edit project" class="btn btn-link mr-3">
              <span class="glyphicon glyphicon-edit"></span>
              <span class="col-xs-12">Edit</span>
            </a> 
            <a href="/projects/project_remove.php?id=<?php echo $project->id;?>" type="submit" title="Remove project" class="btn btn-link">
              <span class="text-danger">
                <span class="glyphicon glyphicon-trash"></span>
                <span class="col-xs-12">Remove</span>
              </span>
            </a>
          </div>
        </div>
      </div>
                <?php
        if (isset($_SESSION["error"])) {
        ?>
        <div class="panel panel-danger">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Oops!</strong></h3>
          </div>
          <div class="panel-body">
            <p>
            <?php
              switch ($_SESSION["error"]) {         
                case "errorcreatingtask":
                default:
                  echo "Sorry, your request could not be processed. Please, try again later.";
                  break;
              }
              $_SESSION["error"] = null;
            ?>
            </p>
          </div>
        </div>
        <?php
        }
        ?>
      <div class="row">
        <div class="col-md-3">
          <p><strong>Owner:</strong> <a href="/admin/user_edit.php?id=<?= $project->owner ?>"><?= $project->owner_object->name ?></a></p>
        </div>
        <div class="col-md-3">
          <p><strong>Creation date:</strong> <?= getFormattedDate($project->creation_date) ?></p>
        </div>
        <div class="col-md-3">
          <?php $icon_class = $project->active ? "ok green" : "remove red"; ?>
          <p><strong>Active?</strong> <span class="glyphicon glyphicon-<?= $icon_class ?>" aria-hidden="true"></span></p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-10">
          <p><strong>Description:</strong> <?= $project->description ?></p>
        </div>
        <div class="col-md-2">
<!--          <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#popup_remove">Remove</button>-->
        </div>
      </div>
      <div class="title-container row vertical-align">
        <div class="col-xs-6 col-md-6">
          <h3>Project Tasks</h3>
        </div>
        <div class="col-xs-6 col-md-6 text-right">
          <a href="/projects/download_sentences.php?project_id=<?= $project->id ?>" class="btn btn-link mr-3" title="Download sentences evaluation as TSV">
            <span class=" glyphicon glyphicon-download"></span>
            <span class="col-xs-12">Download</span>
          </a>
          <a href="/tasks/task_new.php?p_id=<?= $project->id ?>" class="btn btn-link" title="New task">
            <span class="glyphicon glyphicon-plus"></span>
            <span class="col-xs-12">New task</span>
          </a>
          </div>
      </div>

      <hr style="margin-top: 0px;">

      <table id="tasks-table" class="table table-striped table-bordered display responsive nowrap" data-projectid="<?= $project->id ?>" cellspacing="0" style="width:100%;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Assigned user</th>
            <th>SL</th>
            <th>TL</th>
            <th>Size</th>
            <th>Corpus</th>  
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Type</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th>Assigned user</th>
            <th>SL</th>
            <th>TL</th>
            <th>Size</th>
            <th>Corpus</th>  
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Type</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    require_once(TEMPLATES_PATH . "/modals.php")
    ?>

    <input type=hidden id="input_isowner" name="isowner" value="<?php echo (getUserId() == $project->owner || isRoot()) ?>" />
  </body>
</html>


