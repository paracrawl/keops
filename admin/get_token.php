<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/invite_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
//
  require_once(RESOURCES_PATH . "/session.php");
  
  if (!$SIGNEDIN) {
    header("Location: /signin.php");
    die();
  }
  else if (!$ADMIN_VIEW_PERMISSIONS) {
    header("Location: /index.php");
    die();
  }


$missing_params = checkPostParameters(["email", "campaigns"]);

if (count($missing_params) ==0){
  $invite_dao = new invite_dao();
  $token = uniqid();
  $admin = getUserId();
  if ($invite_dao->inviteUser($admin, $token, $email)){
    echo getSignUpURL($token);
  }
  else {
    echo "Error";
  }
  
}
else {
  echo "Missing parameters";
}