<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$failedparams = checkPostParameters(["id", "name", "source_lang", "target_lang"]);

if (count($failedparams) == 0) {
  
  $id = $_POST["id"];
  $name = $_POST["name"];
  $source_lang = $_POST["source_lang"];
  $target_lang = $_POST["target_lang"];
  $active = "false";


  if (isset($_POST["active"]) && $_POST["active"]=="on") {
    $active = "true";
  }
  
  $corpus_dao = new corpus_dao();
  $corpus_dto = corpus_dto::newCorpus($id, $name, $source_lang, $target_lang, $active);

 if ($corpus_dao->updateCorpus($corpus_dto)) {
      $_SESSION["error"] = null;
      header("Location: /admin/index.php#corpora");
      die();      
 }
 else {
      $_SESSION["error"] = "corpusediterror";
      header("Location: /admin/index.php#corpora");
      die();
  }
}
else {
  $_SESSION["error"] = "missingparams";
  header("Location: /admin/index.php#corpora");
  die();
}

  
