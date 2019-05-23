<?php
/**
 * Creates and retreives a new invitation token for a given email address.
 * Retrieves the existing one if the email address is already in use.
 */
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
  
  $user_dto = $user_dao->getUser($email);
  if ($user_dto->id!=null){
    $response = array('error' => 1, 'message' => 'The user is already registered.', 'user_id' => $user_dto->id);
    echo json_encode($response);
  }
  else {
    switch ($invite_dao->inviteUser($invite_dto)){ 
      case "ok":
        $response = array('error' => 0, 'message' => '', 'token_url' => $invite_dto->getInviteUrl());
        echo json_encode($response);
        break;
      case "alreadyexisted": // TODO is this case impossible?
        if ($invite_dto->date_used!=null){
          $response = array('error' => 1, 'message' => 'The user is already registered.');
          echo json_encode($response);
        }
        else {
          $response = array('error' => 0, 'message' => 'User was already invited.', 'token_url' => $invite_dto->getInviteUrl());
          echo json_encode($response);
        }
        break;
      case "error":
      default:
        $response = array('error' => 1, 'message' => 'Sorry, your request could not be processed. Please, try again later.');
        echo json_encode($response);
        break;
    }
  }
}
else {
  $response = array('error' => 1, 'message' => 'Missing parameters');
  echo json_encode($response);
}