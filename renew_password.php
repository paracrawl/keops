<?php
/**
 * Sign-in page
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");

  $PAGETYPE = "public";
  require_once(RESOURCES_PATH . "/session.php");

  if (isSignedIn()) {
    header("Location: /index.php");
    exit();
  }

  $given_token = filter_input(INPUT_GET, 'token');

  if (!$given_token) {
    header("Location: /index.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Sign in</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container signin">
      <div class="signin-page">
        <?php
          if (isset($_SESSION["error"])) {
        ?>
        <div class="panel panel-danger signin-error-panel">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Oops!</strong></h3>
          </div>
          <div class="panel-body">
            <p>
            <?php
              switch ($_SESSION["error"]) {
                case "wrongpassword":
                  echo "Email and password do not match. Please, try again.";
                  break;
                case "expired":
                  echo "The token has expired.";
                  break;
                case "unknownerror":
                default:
                  echo "Sorry, your request could not be processed. Please, try again later.";
                  break;
              }
            ?>
            </p>
          </div>
        </div>
        <?php
        }
        ?>

        <form class="form-signin <?= (isset($_SESSION["error"])) ? "form-signin-error" : "" ?>" role="form" data-toggle="validator" action="users/renew_password.php" method="post">
          <input type=hidden name="token" value="<?= $given_token ?>" />
          <input type=hidden name="service" value="renew" />

          <div class="signin-title">
            <img src="img/pyramids-icon.png" alt="" />
            <div class="h3">Reset your password</div>
          </div>
          <div class="form-group">
            <label for="password" class="sr-only control-label">New password</label>
            <input type="password" name="password" id="password" class="form-control" data-minlength="6" placeholder="New password" required="">
            <div class="help-block with-errors">Minimum of 6 characters</div>
          </div>
          <div class="form-group">
            <label for="password2" class="sr-only control-label">Repeat your new password</label>
            <input type="password" name="password2" id="password2" class="form-control" data-minlength="6" placeholder="Repeat your new password" required="">
            <div class="help-block with-errors">Minimum of 6 characters</div>
          </div>
          <div class="form-group">
            <div class="row vertical-align">
              <div class="col-sm-6 col-xs-6">
                <a href="/signup.php" class="btn btn-lg btn-signup btn-link">Sign up</a>
              </div>
              <div class="col-sm-6 col-xs-6 text-right">
                <button class="btn btn-primary" type="submit">Save</button>
              </div>
            </div>
          </div>
        </form>
    </div>
    
    <footer>
      <div class="prompsit-gradient"></div>
      <div class="prompsit-info">
          <a target="_blank" href="http://www.prompsit.com" rel="noopener">
            <img src="/img/logotipo-prompsit.png" alt="Prompsit home" />
          </a>
      </div>
    </footer>
  </div>
      
    <?php
    $_SESSION["error"] = null;
    $_SESSION["userinfo"] = null;

    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>