<?php
/**
 * Adds a new language.
 * If it succeeds, it redirects to the Languages tab.
 * If it fails, it redirects to the New Language page.
 * 
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/language_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");


$failedparams = checkPostParameters(["langcode", "langname"]);

if (count($failedparams) == 0) {
  
  $langcode = $_POST["langcode"];
  $langname = $_POST["langname"];
  
  $language_dao = new language_dao();
  
  
  $language_dto = language_dto::newLanguage("",$langcode, $langname);

  if ($language_dao->existsLangCode($language_dto->langcode)) {
    $_SESSION["error"] = "existinglangcode";
    header("Location: /admin/new_language.php");
    die();
  }
  if ($language_dao->existsLangName($language_dto->langname)) {
    $_SESSION["error"] = "existinglangname";
    header("Location: /admin/new_language.php");
    die();
  }
 if ($language_dao->addLanguage($language_dto)) {
    header("Location: /admin/index.php#languages");
      die();      
 }
 else {
      $_SESSION["error"] = "unknownerror";
    header("Location: /admin/new_language.php");
      die();
  }
}

else {
  $_SESSION["error"] = "missingparams";
    header("Location: /admin/new_language.php");
      die();
}

  
