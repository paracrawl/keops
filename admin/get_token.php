<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
  //CHECK ADMIN PERMISSIONS !!!

$missing_params = checkPostParameters(["name", "email", "campaigns"]);
if (count($missing_params) ==0){
  echo getSignUpURL($token);
}
else {
  echo "";
}