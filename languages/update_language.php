<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/language_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");


$failedparams = checkPostParameters(["id","langcode", "langname"]);

if (count($failedparams) == 0) {
  $id = $_POST["id"];
  $langcode = $_POST["langcode"];
  $langname = $_POST["langname"];
  
  $language_dao = new language_dao();
  
  
  $language_dto = language_dto::newLanguage($id, $langcode, $langname);

  $langIdByLangCode =  $language_dao->getLangByLangCode($language_dto->langcode);
  $langIdByLangName =  $language_dao->getLangByLangName($language_dto->langname);
   
  
  if (is_numeric($langIdByLangCode->id) && $langIdByLangCode->id!=$language_dto->id ) {
    $_SESSION["error"] = "existinglangcode";
    header("Location: /languages/language_edit.php?id=".$language_dto->id);
    die();
  }
  if (is_numeric($langIdByLangName->id) && $langIdByLangName->id!=$language_dto->id ) {
    $_SESSION["error"] = "existinglangname";
    header("Location: /languages/language_edit.php?id=".$language_dto->id);
    die();
  }
 if ($language_dao->updateLanguage($language_dto)) {
    header("Location: /admin/index.php#languages");
      die();      
 }
 else {
      $_SESSION["error"] = "unknownerror";
    header("Location: /languages/language_edit.php?id=".$language_dto->id);
      die();
  }
}

else {
  $_SESSION["error"] = "missingparams";
    header("Location: /languages/language_edit.php?id=".$language_dto->id);
      die();
}

  
