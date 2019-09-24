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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KEOPS</title>

  <?php
    require_once(TEMPLATES_PATH . "/head.php");
  ?>

  <style>
    .navbar {
      box-shadow: 1px 0px 1em gray;
    }
  </style>
</head>
<body>    
  <?php require_once(TEMPLATES_PATH . "/header.php"); ?>

  <div class="container" style="margin-top: -20px;">
    <div class="row">
      <div class="col-xs-12 col-sm-4">
        <img src="/img/robot.svg" alt="Illustration of a broken robot" class="w-100" />
        <small>
          <a target="_blank" href="https://www.freepik.com/free-vector/hand-drawn-404-error_1587349.htm">Designed by Freepik</a>
        </small>
      </div>

      <div class="col-xs-12 col-sm-8" style="padding: 20px;">
        <h1>Oh, well...</h1>
        <h2>Something went wrong</h2>

        <?php if (isset($_REQUEST['code'])) { ?>
        <div class="mt-5">
          <span class="error-code label label-default text-increase mr-3">Error <?php echo $_REQUEST['code']; ?></span>
          <span class="error-description">
              <?php
              switch ($_REQUEST['code']) {
                case 500:
                  echo "Internal server error";
                  break;
                case 404:
                  echo "File not found";
                  break;
                default:
              }
              ?>
          </span>
        </div>
        <?php } ?>

        <hr class="mt-5" />

        <p class="mt-4">
          <a class="btn btn-link pl-0" href="/"><span class="glyphicon glyphicon-home"></span> Home</a>
          <a class="btn btn-link" href="/contact.php"><span class="glyphicon glyphicon-envelope"></span> Contact support</a>
          </ul>
        </p>
      </div>
    </div>
  </div>

<?php
  require_once(TEMPLATES_PATH . "/home-footer.php");
?>
</body>
</html>
