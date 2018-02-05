<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
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
      //Valid password
      $_SESSION['userinfo'] = $userinfo;
      header("Location: /index.php");
    die();
    } else {
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
    $_SESSION["error"] = "missingdata";   
    $_SESSION["userinfo"] = null;
    header("Location: /signin.php");
    die();
  }
  else {
    $_SESSION["error"] = "unknownerror";
    $_SESSION["userinfo"] = null;
    header("Location: /signin.php");
    die();
  }
}

  
