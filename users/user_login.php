<?php
/**
 * Logs an user in.
 * If it succeeds, it redirects to the index page.
 * If it fails, it redirects to the signin page.
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_langs_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/password_renew_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

$PAGETYPE = "public";
require_once(RESOURCES_PATH . "/session.php");

$failedparams = checkPostParameters(["email", "password"]);
if (count($failedparams) == 0){
  $email = $_POST["email"];
  $password = $_POST["password"];

  //This is the function to store in DB
//  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  $user_dao = new user_dao();
  //$password_hash = $user_dao->getUserPassword($email);
  $userinfo = $user_dao->getUser($email);
  $password_hash = $userinfo->password;
  if ($password_hash == null || $password_hash==""){
    //"Not registered";
    $_SESSION['error']="notregistered";
    $_SESSION['userinfo']=null;
    header("Location: /signin.php");
    die();
  }
  else {
    if (password_verify($password, $password_hash)) {
      echo "valid!";
      //Valid password
      $user_langs_dao = new user_langs_dao();
      $userinfo->langs = $user_langs_dao->getUserLangs($userinfo->id);
      $_SESSION['userinfo'] = $userinfo;

      $password_renew_dao = new password_renew_dao();
      $password_renew_dao->revokeTokenbyUserId($userinfo->id);

      header("Location: /index.php");
    die();
    } else {
      echo "wrong pw";
      //echo "Wrong password";
      $_SESSION['error']="wrongpassword";
      $_SESSION['userinfo']=null;
      header("Location: /signin.php");
    die();
    }
  }
}
else {
  if (in_array("email", $failedparams) || in_array("password", $failedparams)){
    echo "missing mail";
    $_SESSION["error"] = "missingdata";   
    $_SESSION["userinfo"] = null;
    header("Location: /signin.php");
    die();
  }
  else {
    echo "unknown";
    $_SESSION["error"] = "unknownerror";
    $_SESSION["userinfo"] = null;
    header("Location: /signin.php");
    die();
  }
}

  
