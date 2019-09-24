<?php
/**
 * Sign-in page
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");

  $PAGETYPE = "public";
  require_once(RESOURCES_PATH . "/session.php");

  $project_id = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
  $user_id = filter_input(INPUT_GET, 'u', FILTER_SANITIZE_STRING);
  if ($project_id&& !isSignedIn()) { 
      header("Location: /signin.php");
      exit();
  }

    $user_dao = new user_dao();
    if ($user_id) {
        $user = $user_dao->getUserById($user_id);
        if (!$user) {
            header("Location: /signin.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KEOPS | Contact</title>
    <?php require_once(TEMPLATES_PATH . "/head.php"); ?>
</head>
<body>
    <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 col-xs-12">
                <div class="page-header">
                    <h1>
                        <?php if ($project_id) { ?>
                            Contact PM of Project #<?= $project_id ?>
                        <?php } else if ($user) { ?>
                            Contact <?= $user->name ?>
                        <?php } else { ?>
                            Contact support
                        <?php } ?>
                    </h1>
                </div>

                <?php if($_SESSION["contacterror"]) { ?>
                    <div class="alert alert-danger" role="alert">We could not send your message. Please, try again later.</div>
                <?php $_SESSION["contacterror"] = false; } ?>

                <?php if($_SESSION["contactsuccess"]) { ?>
                    <div class="alert alert-success" role="alert">Your message has been successfully sent!</div>
                <?php $_SESSION["contactsuccess"] = false; } ?>

                <form action="/services/contact_service.php" method="post">
                    <?php if ($project_id) { ?>
                        <input type=hidden name="pm" value="<?= $project_id ?>" /> 
                    <?php } ?>
                    <?php if ($user_id) { ?>
                        <input type=hidden name="u" value="<?= $user_id ?>" /> 
                    <?php } ?>

                    <?php 
                        $user = $user_dao->getUserById(getUserId());
                    ?>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type=text class="form-control" id="name" name="name" autocomplete="off" value="<?= ($user) ? $user->name : "" ?>" />
                    </div>
                    <div class="form-group">
                        <label for="from">Email</label>
                        <input type=text class="form-control" id="from" name="from" autocomplete="off" value="<?= ($user) ? $user->email : "" ?>" />
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type=text class="form-control" id="subject" name="subject" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea type=text class="form-control" id="message" name="message"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="copy" class="checkbox-custom active" tabindex="0">
                            <input type="checkbox" id="copy" autocomplete="off" name="copy" checked />
                            <span class="checkbox-control"></span>
                            Send me a copy
                        </label>
                    </div>
                    <?php if (!isSignedIn()) { ?>
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="6LekjLEUAAAAAHmTjsaiYkiARjoXk_3G2affoyju"></div>
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    </div>
                    <?php } ?>
                    <div class="form-group row">
                        <div class="col-md-6 col-xs-6">
                            <a href="javascript:history.back();" class="btn btn-link px-0"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
                        </div>

                        <div class="col-md-6 col-xs-6 text-right">
                            <button type=submit class="btn btn-primary px-5">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once(TEMPLATES_PATH . "/resources.php"); ?>
</body>
</html>