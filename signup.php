<?php    
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");

  $PAGETYPE = "user";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Sign up</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container signup">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>Sign up</h1>
        <p>Please enter the required fields to create your account.</p>

        <div class="panel panel-primary">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Note</strong></h3>
          </div>
          <div class="panel-body">
            <p>If you are here, it means that you were invited to participate in some of the evaluation tasks opened in this system. If you don't have an invitation, you won't be able to register. You will be redirected to the evaluation tasks if you are successfully registered.</p>
          </div>
        </div>
      </div>
      
      <form class="form-horizontal" method="post" action="" role="form" data-toggle="validator">
        <div class="form-group">
          <label for="username" class="col-sm-4 control-label">Username</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="username" id="username" type="text" size="30" value="" aria-describedby="helpUsername" maxlength="50" required="" autofocus="">
            </div>
            <div id="helpUsername" class="help-block with-errors">Please enter your username</div>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-4 control-label">Email</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="email" id="email_address" type="email" size="30" value="" aria-describedby="helpEmail" maxlength="200" required="">
            </div>
            <div id="helpEmail" class="help-block with-errors">Enter your email address</div>
          </div>
          <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="form-group">
          <label for="token" class="col-sm-4 control-label">Access token</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="token" id="token" type="text" size="30" value="" aria-describedby="helpToken" maxlength="50" required="" autofocus=""><?= $token_id ?>
            </div>
            <div id="helpToken" class="help-block with-errors">Please enter your access token</div>
          </div>
          
        <div class="form-group">
          <label for="inputPassword" class="control-label col-sm-4">Password</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input type="password" data-minlength="6" class="form-control" id="inputPassword" required>
            </div>
            <div class="help-block">Minimum of 6 characters</div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPasswordConfirm" class="control-label col-sm-4">Confirm password</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input type="password" class="form-control" id="inputPasswordConfirm" data-match="#inputPassword" data-match-error="Password doesn't match" required>
            </div>
            <div class="help-block with-errors"></div>
          </div>
        </div>
        <button type="submit" class="col-sm-offset-4 btn btn-primary">Sign up</button>

      </form>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>
