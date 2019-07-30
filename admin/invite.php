<?php
/**
 * Page to create a new invitation
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html lang="en">
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
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#invitations">Invitations</a></li>
        <li class="active">Invite user</li> 
      </ul>
      <div class="page-header">
        <h1>Invite new user</h1>
      </div>
      <form class="form row" role="form" data-toggle="validator">
        <div class="form-group col-xs-12 col-md-12 row vertical-align">
          <div class="col-sm-1 col-xs-2">
            <div class="number-container text-increase">1</div>
          </div>
          <div class="col-sm-11 col-xs-10">
            <label for="email" class="control-label">Enter the email address of the user you want to invite</label> <br />
            <input id="email" type="email" name="email" class="form-control" aria-describedby="helpEmail" placeholder="Email address" maxlength="200" required="" autofocus="">
            <div id="helpEmail" class="help-block with-errors">The email should be valid, since it will be used to notify the user</div>
          </div>
        </div>
        <div class="form-group col-xs-12 col-md-12 row" style="margin-top: 2em;">
          <div class="invitation_token vertical-align">
            <div class="col-sm-1 col-xs-2">
              <div class="number-container text-increase">2</div>
              </div>
            <div class="col-sm-11 col-xs-10" id="invitation_url_controls">
              <label for="token" class="control-label">Get the invitation URL</label> <br />
              <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span></div>
                <input id="token" class="form-control" readonly aria-describedby="helpInvitation" placeholder="The invitation URL will appear here" maxlength="200">
              </div>

              <div id="tokenHelp" class="help-block with-errors text-success">An email will be sent to the new user with the invitation URL</div>
            </div>
          </div>
        </div>
        <div class="form-group col-xs-12 col-md-12 row" style="margin-top: 2em;">
          <div class="col-xs-12 col-md-12 text-right">
            <a href="/admin/index.php#invitations" class="btn btn-info">Cancel</a>
            <button type="submit" id="invite_button" class="btn btn-success">Invite</button>
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