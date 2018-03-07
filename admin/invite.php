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
              <ul class="breadcrumb">
        <li><a href="/admin">Management</a></li>
        <li><a href="/admin/#invitations">Invitations</a></li>
        <li class="active">Invite user</li> 
      </ul>
      <div class="page-header">
        <h1>Invite new user</h1>
      </div>
      <form class="form-horizontal" role="form" data-toggle="validator">
        <div class="form-group">
          <label for="email" class="col-sm-1 control-label">Email</label>
          <div class="col-sm-7">
            <input id="email" type="email" name="email" class="form-control" aria-describedby="helpEmail" placeholder="Email address" maxlength="200" required="" autofocus="" tabindex="1">
            <div id="helpEmail" class="help-block with-errors">1. Enter the email address you want to invite</div>
          </div>
        </div>
        <div class="form-group">
          <div class="invitation_token">
            <label for="token" class="col-sm-1 control-label">Invitation</label>
            <div class="col-sm-7">
              <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span></div>
                <input id="token" class="form-control" readonly aria-describedby="helpInvitation" placeholder="The invitation URL will appear here" maxlength="200" tabindex="2">
              </div>
              <div id="helpInvitation" class="help-block with-errors">2. Please copy this URL when generated and send to the new user. They will be able to sign up with the token ID.</div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-5 text-right">
            <a href="/admin/#invitations" class="col-sm-offset-1 btn btn-danger" tabindex="4">Cancel</a>
            <button type="submit" id="invite_button" class="col-sm-offset-1 btn btn-success" tabindex="3">Invite</button>
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