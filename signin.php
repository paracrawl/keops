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
    <title>KEOPS | Sign up</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container signin">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="page-header">
        <h1>Sign in</h1>
        <p>Please enter your username and password to access the system.</p>
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
      </div>
      <form class="form-signin" role="form" data-toggle="validator" action="users/user_login.php" method="post">
        
        <div class="form-group">
          <label for="email" class="sr-only control-label">Email address</label>
          <input type="email" name="email" class="form-control" placeholder="Email address" required="" autofocus="">
          <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
          <div class="help-block with-errors">Enter your email address</div>
        </div>
        <div class="form-group">
          <label for="password" class="sr-only control-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Password" required="">
          <div class="help-block with-errors">Enter your password</div>
        </div>
        <div class="form-group">
          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </div>
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
