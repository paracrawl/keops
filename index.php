<?php
// load up your config file
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
$PAGETYPE = "public";
require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Home</title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div id="header">
        <h1>Welcome to KEOPS</h1>
      </div>
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
                case "signuploggedin":     
                  echo "Please, logout before signing up a different user.";
                  break;
                case "error":     
                default:
                  echo "Sorry, your request could not be processed. Please, try again later.";
                  break;
              }
              $_SESSION["error"] = null;
            ?>
            </p>
          </div>
        </div>
        <?php
        }
        ?>
      
      <?php
      //This should be in the header
      if (isset($_SESSION["userinfo"])) {
        //$user = new user_dto();
        //$user = $user_dao->getUser("mbanon");
        $user = $_SESSION["userinfo"];
        if (!$user->active == true) {
          ?>        
            <div class="panel panel-danger">
              <div class="panel-heading">
                <h3 class="panel-title"><strong>Oops!</strong></h3>
              </div>
              <div class="panel-body">
              <p>
                Your account has been disabled. Please, contact an administrator if you think this is an error.
              </p>
            </div>
          </div>
        <?php } else { ?>
        <div class="page-header">
          <h3>Your tasks</h3>
          <p>Check the status of your current tasks</p>
        </div>
        <table id="tasks-table" class="table table-striped table-bordered" data-projectid="" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Assigned user</th>
              <th>Size</th>
              <th>Status</th>
              <th>Creation date</th>
              <th>Assigned date</th>
              <th>Completed date</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>ID</th>
              <th>Assigned user</th>
              <th>Size</th>
              <th>Status</th>
              <th>Creation date</th>
              <th>Assigned date</th>
              <th>Completed date</th>
            </tr>
          </tfoot>
        </table>
          <?php
        }
      }
      ?>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>
