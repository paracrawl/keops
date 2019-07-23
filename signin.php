<?php
/**
 * Sign-in page
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");

  $PAGETYPE = "public";
  require_once(RESOURCES_PATH . "/session.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Sign in</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container signin">
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
              case "notregistered":     
              case "wrongpassword":
                echo "Email and password do not match. Please, try again.";
                break;
              case "missingdata":
                echo "You should specify your email and password. Please, try again.";
                break;
              case "unknownerror":
              default:
                echo "Sorry, your request could not be processed. Please, try again later.";
                break;
            }
            $_SESSION["error"] = null;
            $_SESSION["userinfo"] = null;
          ?>
          </p>
        </div>
      </div>
      <?php
      }
      ?>

      <div class="signin-page">
        <form class="form-signin" role="form" data-toggle="validator" action="users/user_login.php" method="post">
          <div class="signin-title">
            <img src="img/pyramids-icon.png" alt="" />
            <div class="h3">Sign in</div>
          </div>
          <div class="form-group">
            <label for="email" class="sr-only control-label">Email address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email address" required="" autofocus="">
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            <div class="help-block with-errors">Enter your email address</div>
          </div>
          <div class="form-group">
            <label for="password" class="sr-only control-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">
            <div class="help-block with-errors">Enter your password</div>
          </div>
          <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
          </div>
        </form>

        <footer>
          <div class="prompsit-gradient"></div>
          <div class="prompsit-info">
              <a target="_blank" href="http://www.prompsit.com">
                <img src="http://www.prompsit.com/wp-content/themes/prompsit-theme/images/logotipo-prompsit.png" alt="" />
              </a>
          </div>
      </footer>
    </div>
  </div>
      
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>
