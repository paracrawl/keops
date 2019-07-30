<?php
/**
 * Page to create a new project
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html lang="en">
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
        
        <div class="row">
          <div class="col-xs-12 col-md-10 col-md-offset-1">
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
              <label for="name" class="col-sm-2 control-label">Name</label>
              <div class="col-sm-10">
                <input type="text" name="name" class="form-control" aria-describedby="helpName" placeholder="Name" maxlength="100" required="" autofocus="">
                <div id="helpName" class="help-block with-errors">Write the public name of the project</div>
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">Description</label>
              <div class="col-sm-10">
                <textarea name="description" class="form-control" aria-describedby="helpDesc" placeholder="Description" maxlength="500" required=""></textarea>
                <div id="helpDesc" class="help-block with-errors"></div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-8 col-sm-offset-4 text-right">
                <a href="/admin/index.php#projects" class="btn btn-info">Cancel</a>
                <button type="submit" class="btn btn-success">Save</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>