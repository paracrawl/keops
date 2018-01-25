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
    echo "<h2>" . $user->username . " (" . $user->name . ")</h2>";
    echo "<a href=\"/users/user_logout.php\">Logout</a>";
  } else {
    if (isset($_SESSION["error"])) {
      switch ($_SESSION["error"]) {
        // These errors should be displayed in a fancier way (popup warnings or so)
        case "notregistered":
          echo "User is not registered.";
          $_SESSION["error"] = null;
          break;
        case "wrongpassword":
          echo "Email and password do not match";
          $_SESSION["error"] = null;
          break;
        case "missingdata":
          echo "Please introduce both email and password.";
          $_SESSION["error"] = null;
          break;
        case "unknownerror":
        default:
          echo "An error occurred. Please try again later.";
          $_SESSION["error"] = null;
          break;
      }
      $_SESSION["userinfo"] = null;
    }
    else {
      echo "Not logged in.";
    }
  }
    ?>
  </div>
<?php
require_once(TEMPLATES_PATH . "/footer.php");
?>

