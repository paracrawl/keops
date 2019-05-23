<?php
/**
 * Updates or removes a corpus, and then redirects to the "corpora" tab
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

if (isset($_POST["id"]) && isset($_POST["action"]) && $_POST["action"] == "remove") {
  //It's a corpus removal
  $corpus_id = $_POST["id"];
  $corpus_dao = new corpus_dao();
  if ($corpus_dao->removeCorpus($corpus_id)) {
    $_SESSION["error"] = null;
    header("Location: /admin/index.php#corpora");
    die();
  } else {
    $_SESSION["error"] = "corpusremoveerror";
    header("Location: /admin/index.php#corpora");
    die();
  }
} else {

  $failedparams = checkPostParameters(["id", "name", "source_lang", "target_lang"]);

  if (count($failedparams) == 0) {

    $id = $_POST["id"];
    $name = $_POST["name"];
    $source_lang = $_POST["source_lang"];
    $target_lang = $_POST["target_lang"];
    $active = "false";


    if (isset($_POST["active"]) && $_POST["active"] == "on") {
      $active = "true";
    }

    $corpus_dao = new corpus_dao();
    $corpus_dto = corpus_dto::newCorpus($id, $name, $source_lang, $target_lang, $active);

    if ($corpus_dao->updateCorpus($corpus_dto)) {
      $_SESSION["error"] = null;
      header("Location: /admin/index.php#corpora");
      die();
    } else {
      $_SESSION["error"] = "corpusediterror";
      header("Location: /admin/index.php#corpora");
      die();
    }
  } else {
    $_SESSION["error"] = "missingparams";
    header("Location: /admin/index.php#corpora");
    die();
  }
}

