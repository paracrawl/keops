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
      <?php require_once(TEMPLATES_PATH . "/header.php"); 
      if  (!isSignedIn()) { ?>
        <header class="masthead     text-white text-center">
          <div class="container">
              <img class="img-fluid mb-5 d-block mx-auto" src="pyramids-icon.png" alt="">
            <h1 class=" mb-0">Welcome to KEOPS</h1>
            <hr class="star-light">
            <h2 class="font-weight-light mb-0">Keen Evaluation of Parallel Sentences</h2>
          </div>
        </header>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card text-center  ">
<!--            <img class="card-img-top" src="http://placehold.it/500x325" alt="">-->
            <span class="glyphicon glyphicon-upload"></span>
            <div class="card-body">
              <h4 class="card-title">Card title</h4>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus neque.</p>
            </div>
<!--            <div class="card-footer">
              <a href="#" class="btn btn-primary">Find Out More!</a>
            </div>-->
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card text-center  ">
<!--            <img class="card-img-top" src="http://placehold.it/500x325" alt="">-->
              <span class="glyphicon glyphicon-user"></span>
            <div class="card-body">
              <h4 class="card-title">Card title</h4>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo magni sapiente, tempore debitis beatae culpa natus architecto.</p>
            </div>
<!--            <div class="card-footer">
              <a href="#" class="btn btn-primary">Find Out More!</a>
            </div>-->
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card text-center  ">
<!--            <img class="card-img-top" src="http://placehold.it/500x325" alt="">-->
            <span class="glyphicon glyphicon-pencil"></span>
            <div class="card-body">
              <h4 class="card-title">Card title</h4>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus neque.</p>
            </div>
<!--            <div class="card-footer">
              <a href="#" class="btn btn-primary">Find Out More!</a>
            </div>-->
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card text-center  ">
<!--            <img class="card-img-top" src="http://placehold.it/500x325" alt="">-->
            <span class="glyphicon glyphicon-ok"></span>
            <div class="card-body">
              <h4 class="card-title">Card title</h4>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus neque.</p>
            </div>
<!--            <div class="card-footer">
              <a href="#" class="btn btn-primary">Find Out More!</a>
            </div>-->
          </div>
        </div>
            
        <?php
      }else{
            ?>
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
          <p>Check the status of the tasks assigned to you</p>
        </div>
        <table id="user-tasks-table" class="table table-striped table-bordered" data-projectid="" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Project</th>
              <th title="Source language">SL</th>
              <th title="Target language">TL</th>
              <th>Size</th>
              <th>Status</th>
              <th>Creation date</th>
              <th>Actions</th>
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
              <th>Actions</th>
            </tr>
          </tfoot>
        </table>
          <?php
        }
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
