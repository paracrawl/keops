<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

$PAGETYPE = "public";
require_once(RESOURCES_PATH . "/session.php");

$failedparams = checkPostParameters(["name", "email", "token", "password"]);

if (count($failedparams) == 0){

  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
  $name = $_POST["name"];
  $token = $_POST["token"];

  //This is the function to store in DB
//  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  $user_dao = new user_dao();
  //$password_hash = $user_dao->getUserPassword($email);

  //$username_exists = $user_dao->checkUsername($username);
  $email_exists = count($user_dao->getUser($email)) > 0;

  if ($email_exists){
    $_SESSION["error"] = "alreadyregistered";
    $_SESSION["userinfo"] = null;
    header("Location: /signup.php");
    die();
  }
  else {
    $invite_dao = new invite_dao();
    $invite_dto = new invite_dto("", $email, $token);
    if ($invite_dao->checkToken($invite_dto)) {
      $user_dto = new user_dto();
      $user_dto->email = $email;
      $user_dto->name = $name;
      $user_dto->token = $token;
      $user_dto->password = $password;
      $user_dao->newUser($user_dto);
      if ($user_dto->id > 0){
        $invite_dao->markAsUsed($invite_dto);
        $SESSION["userinfo"] = $user_dto;
        header("Location: /index.php");
        die();
      } else {
        $SESSION["error"] = "signupfailed";
        $_SESSION["userinfo"] = null;
        header("Location: /signup.php");
        die();
      }
    } else {
      $_SESSION["error"] = "tokennotmatching";
      $_SESSION["userinfo"] = null;
      header("Location: /signup.php");
      die();
    }
  }

}
else {
    $_SESSION["error"] = "missingdata";   
    $_SESSION["userinfo"] = null;
    header("Location: /signin.php");
    die();
  }

  
