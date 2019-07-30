<?php
/*
 * Sign-up page, to create an account
 */
// load up your config file
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");

$PAGETYPE = "public";
require_once(RESOURCES_PATH . "/session.php");

if ($SIGNEDIN) {
    //A logged user cannot create a new account until logging out
  $_SESSION["error"] = "signuploggedin";
  header("Location: /index.php");
  die();
}

$token_id = null;
$email = null;
if (isset($_GET["token"]) && $_GET["token"] != null && $_GET["token"] != "") {
  $token_id = $_GET["token"];
}
if (isset($_GET["email"]) && $_GET["email"] != null && $_GET["email"] != "") {
  $email = $_GET["email"];
}

$language_dao = new language_dao();
$languages = $language_dao->getLanguages();

?>
<!DOCTYPE html>
<html lang="en">
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
                    case "alreadyregistered":
                      echo "The email address is already in use by another account.";
                      $_SESSION["error"] = null;
                      break;
                    case "emailnotfound":
                      echo "The email address you provided hasn't been invited to join Keops.";
                      $_SESSION["error"] = null;
                      break;
                    case "tokennotmatching":
                      echo "Email and token do not match. Please, try again.";
                      $_SESSION["error"] = null;
                      break;
                    case "missingdata":
                      echo "All fields are mandatory. Please, try again.";
                      $_SESSION["error"] = null;
                      break;
                    case "signupfailed":
                    case "error":
                    default:
                      echo "Sorry, your request could not be processed. Please, try again later.";
                      $_SESSION["error"] = null;
                      break;
                  }
                  ?>
                </p>
              </div>
          </div>
        <?php
        }
        ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Note</strong></h3>
          </div>
          <div class="panel-body">
            <p>If you are here, it means that you were invited to participate in some of the evaluation tasks opened in this system. If you don't have an invitation, you won't be able to register. You will be redirected to the evaluation tasks if you are successfully registered.</p>
          </div>
        </div>
      </div>

      <form class="form-horizontal" method="post"  action="users/user_signup.php" role="form" data-toggle="validator">
        <div class="form-group">
          <label for="name" class="col-sm-5 control-label">Name</label>
          <div class="col-sm-5">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="name" id="name" type="text" size="30" value="" aria-describedby="helpName" maxlength="50" required="" autofocus="">
            </div>
            <div id="helpName" class="help-block with-errors">Please enter your name</div>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-5 control-label">Email</label>
          <div class="col-sm-5">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="email" id="email_address" type="email" size="30" aria-describedby="helpEmail" maxlength="200" required="" value="<?= $email ?>">
            </div>
            <div id="helpEmail" class="help-block with-errors">Enter your email address</div>
          </div>
          <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="form-group">
          <label for="token" class="col-sm-5 control-label">Access token</label>
          <div class="col-sm-5">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="token" id="token" type="text" size="30"  aria-describedby="helpToken" maxlength="50" required="" autofocus="" value="<?= $token_id ?>">
            </div>
            <div id="helpToken" class="help-block with-errors">Please enter your access token</div>
          </div>
        </div>
        <div class="form-group">
          <label for="langs[]" class="control-label col-sm-5">Languages</label>
          <div class="col-sm-5">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <select multiple class="form-control" name="langs[]" id="langs" required="">
                <?php foreach ($languages as $lang) { ?>
                  <option value="<?= $lang->id ?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="help-block">Hold the CTRL key to select several languages.</div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword" class="control-label col-sm-5">Password</label>
          <div class="col-sm-5">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input name="password" type="password" data-minlength="6" class="form-control" id="inputPassword" required>
            </div>
            <div class="help-block">Minimum of 6 characters</div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPasswordConfirm" class="control-label col-sm-5">Confirm password</label>
          <div class="col-sm-5">
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
