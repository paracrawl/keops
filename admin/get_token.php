<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/invite_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");


$missing_params = checkPostParameters(["email"]);

if (count($missing_params) ==0){
  
  $email = $_POST["email"];
  $admin = getUserId();
  
  $invite_dao = new invite_dao();
  $token = uniqid();
  $invite_dto = new invite_dto($admin, $email, $token);
  $user_dao = new user_dao();
  
  if ($user_dao->getUser($email)-> id!=null){
    echo "The user is already registered.";
  }
  else {
    switch ($invite_dao->inviteUser($invite_dto)){ 
      case "ok":      
          echo "".$invite_dto->getInviteUrl();
        break;
      case "alreadyexisted":
        if ($invite_dto->date_used!=null){
          echo "The user is already registered.";
        }
        else {
          echo "User was already invited: ".$invite_dto->getInviteUrl();
        }
        break;
      case null:
      default:
        error_log("WtF");
        break;
    }
  }
}
else {
  echo "Missing parameters";
}