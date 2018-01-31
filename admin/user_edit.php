<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(RESOURCES_PATH . "/session.php");

if (!$SIGNEDIN) {
  header("Location: /signin.php");
  die();
}
else if (!$ADMIN_VIEW_PERMISSIONS) {
  header("Location: /index.php");
  die();
}

$user_dao = new user_dao();
$user = $user_dao->getUserById(filter_input(INPUT_GET, "id"));
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Edit User <?= $user->id ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>Edit user</h1>
      </div>
      <form class="form-horizontal" method="post" action="" role="form" data-toggle="validator">
        <div class="form-group">
          <label for="username" class="col-sm-1 control-label">Username</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="username" id="username" type="text" size="30" value="<?= $user->username ?>" aria-describedby="helpUsername" required="" autofocus="">
            </div>
            <div id="helpUsername" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-1"></div>
          <a class="col-sm-4" href="">Change password (TODO)</a>
        </div>
        <div class="form-group">
          <label for="username" class="col-sm-1 control-label">Name</label>
          <div class="col-sm-4">
            <input class="form-control" name="name" id="name" type="text" size="30" value="<?= $user->name ?>" aria-describedby="helpName" autofocus="">
            <div id="helpName" class="help-block with-errors"></div>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-1 control-label">Email</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="email" id="email_address" type="email" size="30" value="<?= $user->email ?>" aria-describedby="helpEmail"  required="">
            </div>
            <div id="helpEmail" class="help-block with-errors"></div>
          </div>
          <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="form-group">
          <label for="role" class="control-label col-sm-1">Role</label>
          <div class="col-sm-4">
            <select class="form-control" name="role" id="role">
              <option value="ADMIN" <?= $user->role == "ADMIN" ? "selected" : "" ?>>ADMIN</option>
              <option value="STAFF" <?= $user->role == "STAFF" ? "selected" : "" ?>>STAFF</option>
              <option value="USER" <?= $user->role == "USER" ? "selected" : "" ?>>USER</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="active" class="control-label col-sm-1">Active?</label>
          <div class="col-sm-4">
            <input type="checkbox" class="checkbox" name="active" id="active" <?= $user->active ? "checked" : "" ?>>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/#users" class="col-sm-offset-1 btn btn-danger">Cancel</a>
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