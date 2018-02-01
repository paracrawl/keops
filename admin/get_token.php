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


$missing_params = checkPostParameters(["email", "projects"]);

if (count($missing_params) ==0){
  
  $email = $_POST["email"];
  $admin = getUserId();
  
  $invite_dao = new invite_dao();
  $invite_dto = new invite_dto($admin, $email);
  
  switch ($invite_dao->inviteUser($invite_dto)){ 
    case "ok":      
        echo $invite_dto->getInviteUrl();
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
      break;
  }
  
}
else {
  echo "Missing parameters";
}