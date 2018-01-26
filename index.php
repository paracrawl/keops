<?php
// load up your config file
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/dao/user_dao.php");
require_once(TEMPLATES_PATH . "/header.php");

if(!isset($_SESSION)) { 
        session_start(); 
    } 
?>
<div class="container">
  <div id="header">
    <h1>Welcome to KEOPS</h1>
  </div>
 
  <?php
  //This should be in the header
  if (isset($_SESSION["userinfo"])) {
    //$user = new user_dto();
    //$user = $user_dao->getUser("mbanon");
    $user = $_SESSION["userinfo"];
    echo "<h3>" . $user->username . " (" . $user->name . ")</h3>";
    echo "<h2>Projects</h2>";
    echo "Project 1<br>";
    echo "Project 2<br>";
    if ($user->isAdmin() || $user->isStaff()) {

      echo("<a href=\"admin/admin.php\">Manage projects</a>");
    }
    echo "<a href=\"/users/user_logout.php\">Logout</a>";
  } else {
    echo "Not logged in.";
  }
  ?>
  </div>
<?php
require_once(TEMPLATES_PATH . "/footer.php");
?>

