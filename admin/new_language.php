<?php
/*
 * Page to add a new language
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | New language</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
            <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#languages">Languages</a></li>
        <li class="active">New language</li> 
      </ul>
      <div class="page-header">
        <h1>Add new language</h1>
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
              case "existinglangcode":
                echo "The language code is already taken.";
                break;
              case "existinglangname":
                echo "The language name is already taken.";
                break;
              case "missingdata":
                echo "You should specify both language code and language name. Please, try again.";
                break;
              case "unknownerror":
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
      <form class="form-horizontal" method="post" action="../languages/add_language.php" role="form" data-toggle="validator">
        <div class="form-group">
          <label for="langcode" class="col-sm-1 control-label">Language code</label>
          <div class="col-sm-7">
            <input id="langcode" name="langcode" class="form-control" aria-describedby="helpLangCode" placeholder="Language code" maxlength="5" required="" autofocus="" tabindex="1">
            <div id="helpLangCode" class="help-block with-errors">Two-letter <a target="_blank" href="http://www.loc.gov/standards/iso639-2/php/code_list.php">ISO 639-1</a> language code</div>
          </div>
        </div>
        <div class="form-group">
            <label for="langname" class="col-sm-1 control-label">Language name</label>
            <div class="col-sm-7">
                <input id="langname" name="langname" class="form-control" aria-describedby="helpLangName" placeholder="Language name" maxlength="200" tabindex="2" required="">
              <div id="helpLangName" class="help-block with-errors"></div>
            </div>
        </div>
        <div class="form-group">
          <div class="col-sm-5 text-right">
            <a href="/admin/index.php#languages" class="col-sm-offset-1 btn btn-info" tabindex="4">Cancel</a>
            <button type="submit" id="add_lang_button" class="col-sm-offset-1 btn btn-success" tabindex="3">Save</button>
          </div>
        </div>
      </form>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>