<?php

require_once($_SERVER['DOCUMENT_ROOT'] ."/dao/user_dao.php");
require_once($_SERVER['DOCUMENT_ROOT'] ."/utils/utils.php");


session_start();

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
    echo "Not registered";
  }
  else {
    if (password_verify($password, $password_hash)) {
      //Valid password
      $_SESSION['email'] = $email;
      $_SESSION['userinfo'] = $userinfo;
    } else {
      echo "Wrong password";
    }
  }
}
else {
  echo "Missing params";
}

  
