<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Invite new user</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>Invite new user</h1>
      </div>
      <form class="form-horizontal" action="get_token.php" role="form" method="post" data-toggle="validator">
        <div class="form-group">
          <label for="email" class="col-sm-1 control-label">Email</label>
          <div class="col-sm-4">
            <input type="email" name="email" class="form-control" aria-describedby="helpEmail" placeholder="Email address" maxlength="200" required="" autofocus="">
            <div id="helpEmail" class="help-block with-errors">Enter the email address you want to invite</div>
          </div>
        </div>
<!--        <div class="form-group">
          <label for="projects" class="col-sm-1 control-label">Projects</label>
          <div class="col-sm-4">
            <input type="text" name="projects" class="form-control" aria-describedby="helpProjects" placeholder="Projects">
            <div id="helpProjects" class="help-block with-errors">Select the projects (TODO)</div>
          </div>
        </div>-->
<!--        Name: <input type="text" name="name2" id="name"><br>
        E-mail: <input type="text" name="email2" id="email"><br>
        Campaigns: <input type="text" name="campaigns2" id="campaigns" value="NONE"><br>-->
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/#invitations" class="col-sm-offset-1 btn btn-danger">Cancel</a>
            <button type="submit" class="col-sm-offset-1 btn btn-success">Invite</button>
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