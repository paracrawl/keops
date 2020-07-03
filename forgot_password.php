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
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Recover password</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container signin">
      <div class="signin-page">
        <?php
          if (isset($_SESSION["error"]) && $_SESSION["error"] != "none") {
        ?>
        <div class="panel panel-danger signin-error-panel">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Oops!</strong></h3>
          </div>
          <div class="panel-body">
            <p>
            <?php
              switch ($_SESSION["error"]) {
                case "wrongmail":
                  echo "Email address is not correct. Please, try again.";
                  break;
                case "expired":
                  echo "This token has expired. Please, try again.";
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
          <input type=hidden name="service" value="send" />
          <div class="signin-title">
            <img src="img/pyramids-icon.png" alt="" />
            <div class="h3">Reset your password</div>
          </div>
          <div class="form-group">
          <label for="email" class="sr-only control-label">Email address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email address" required="" autofocus="">
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            <div class="help-block with-errors">Type your email address</div>
          </div>
          <div class="form-group">
            <div class="row vertical-align">
              <div class="col-sm-6 col-xs-6">
                <a href="/signin.php" class="btn btn-lg btn-signup btn-link">Sign in</a>
              </div>
              <div class="col-sm-6 col-xs-6 text-right">
                <button class="btn btn-primary" type="submit">Send</button>
              </div>
            </div>
          </div>

          <?php if ($_SESSION["error"] == "none") { ?>
          <div class="form-group">
            <span class="label label-success">
              <span class="glyphicon glyphicon-ok"></span> Done!
            </span>

            <p class="mt-3">
              You will receive an email with instructions to reset your password.
            </p>
          </div>
          <?php } ?>
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