<?php
/**
 * Index page for regular (non-admin) users
 */
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

      <?php
      if  (!isSignedIn()) {
        ?>
    <body class="home-body">    
      <div class="full-width">
          <div class="container">
            <div class="home-banner row">
              <div class="col-xs-10 col-md-10">
                <img src="img/pyramids-icon-white.png" alt="" />
                <h1>Welcome to KEOPS</h1>
                <h2>Keen Evaluation Of Parallel Sentences</h2>
              </div>

              <div class="col-xs-10 col-md-10" style="padding-top: 4em;">
                <a href="/signin.php" class="btn btn-default outline inverted">Sign in</a>
                <a href="/signup.php" class="btn btn-link inverted">Sign up with a token</a>
              </div>
            </div>
          </div>
      </div>

      <div class="grey-background container">
          <div class="card-container row">         
            <div class="col-md-3">
              <div class="card text-center  ">
                <span class="glyphicon glyphicon-eye-open"></span>
                <div class="card-body">
                  <h4 class="card-title">See your tasks</h4>
                  <p class="card-text">Your personal dashboard will tell you which tasks you've been assigned and practical details about them.</p>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card text-center  ">
                <span class="glyphicon glyphicon-pencil"></span>
                <div class="card-body">
                  <h4 class="card-title">Action!</h4>
                  <p class="card-text">Start or resume an evaluation task, read the guidelines, annotate sentences, send comments and contact your PM in case of doubt.</p>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card text-center  ">
                <span class="glyphicon glyphicon-stats"></span>
                <div class="card-body">
                  <h4 class="card-title">Project stats</h4>
                  <p class="card-text">At any point of completion of a task, see its status and a recap of the work done. Download your own results.</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card text-center">
                <span class="glyphicon glyphicon-ok"></span>
                <div class="card-body">
                  <h4 class="card-title">Done!</h4>
                  <p class="card-text">Communicate with your PM whenever you need it. Easily send your finished task.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
      }else{
        
            ?>
      <body>
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <div class="container">
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
          <p>Check the status of the tasks assigned to you</p>
        </div>
        <table id="user-tasks-table" class="table table-striped table-bordered display responsive nowrap" data-projectid="" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Project</th>
              <th title="Source language">SL</th>
              <th title="Target language">TL</th>
              <th>Size</th>
              <th>Status</th>
              <th>Creation date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>ID</th>
              <th>Project</th>
              <th title="Source language">SL</th>
              <th title="Target language">TL</th>
              <th>Size</th>              
              <th>Status</th>
              <th>Creation date</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
          <?php
        }
      }
        }
      
          ?>


    </div>
              </div>

<?php
if (isSignedIn()) {
  require_once(TEMPLATES_PATH . "/footer.php");
} else {
  require_once(TEMPLATES_PATH . "/home-footer.php");
}
?>
<?php
require_once(TEMPLATES_PATH . "/resources.php");
?>
  </body>
</html>
