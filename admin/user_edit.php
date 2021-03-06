<?php
/*
 * Page to modify the user profile
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_langs_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");


$user_dao = new user_dao();
$user = $user_dao->getUserById(filter_input(INPUT_GET, "id"));

$user_langs_dao = new user_langs_dao();
$user_langs = $user_langs_dao->getUserLangs(filter_input(INPUT_GET, "id"));

$language_dao = new language_dao();
$languages = $language_dao->getLanguages();

?>
<!DOCTYPE html>
<html lang="en">
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
      <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#users">Users</a></li>
        <li><a href="/admin/user_edit.php?id=<?php echo $user->id; ?>"><?= $user->name?></a></li>
        <li class="active">Edit user</li> 
      </ul>

      <?php if ($_SESSION["error"] == "yes") { ?>
      <p class="text-danger">
        Password could not be changed.
      </p>
      <?php } else if ($_SESSION["error"] == "none") { ?>
      <p class="text-success">
        Password changed successfuly.
      </p>
      <?php } ?>

      <?php $_SESSION["error"] = null; ?>

      <div class="page-header">
        <h1>Edit user</h1>
      </div>
      <form class="form-horizontal" method="post" action="../users/user_edit.php" role="form" data-toggle="validator">
        <div class="form-group">
          <label for="name" class="col-sm-1 control-label">Name</label>
          <div class="col-sm-4">
            <input class="form-control" name="name" id="name" type="text" size="30" value="<?= $user->name ?>" aria-describedby="helpName" autofocus="">
            <div id="helpName" class="help-block with-errors"></div>
          </div>
        </div>
        <?php if (isRoot()) { ?>
        <div class="form-group">
          <label for="name" class="col-sm-1 control-label">Password</label>
          <div class="col-sm-4">
            <a href="#" class="btn btn-secondary w-100" data-toggle="modal" data-target="#password_modal">Click to generate a  new one</a>
            <div class="help-block with-errors"></div>
          </div>
        </div>
        <?php } ?>
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
        <label for="langs[]" class="control-label col-sm-1">Languages</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <select multiple class="form-control" name="langs[]" id="langs" required="">
                <?php foreach ($languages as $lang) { ?>
                <option value="<?= $lang->id ?>" <?php if (in_array_field($lang->id, "lang_id", $user_langs)) {echo (" selected = 'true' ");} ?>><?= $lang->langcode . " - " . $lang->langname ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="help-block d-xs-none">Hold the <?= (preg_match("/(macintosh|mac os x)/i", $_SERVER['HTTP_USER_AGENT']) ? "CMD" : "CTRL") ?> key to select several languages.</div>
          </div>
        </div>
               
        <div class="form-group">
          <label for="role" class="control-label col-sm-1">Role</label>
          <div class="col-sm-4">
            <select class="form-control" name="role" id="role">
              <option value="PM" <?= $user->role == "PM" ? "selected" : "" ?>>PROJECT MANAGER</option>
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
        <input type="hidden" name="id" id="id" value="<?= $user->id ?>">
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/admin/index.php#users" class="col-sm-offset-1 btn btn-info">Cancel</a>
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

    <?php if (isRoot()) { ?>
    <?php $rand = bin2hex(random_bytes(5)); ?>
    <form class="modal" tabindex="-1" role="dialog" id="password_modal" action="../users/user_edit.php" method="post">
      <input type=hidden name="id" value="<?php echo $user->id; ?>" />
      <input type=hidden name="new_password" value="<?php echo $rand; ?>" />
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Change password</h4>
          </div>
          <div class="modal-body">
            <p>
              This password will be <strong>assigned to this user</strong>.
            </p>

            <div class="jumbotron text-center py-4">
              <div class="h2 my-0">
                <?php
                  echo $rand;
                ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </form>
    <?php } ?>
  </body>
</html>