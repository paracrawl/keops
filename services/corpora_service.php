<?php
/**
 * Project services.
 * Currently "new", that creates a new project and redirects to the Projects tab,
 *  and "list_dt", that serves the datatables content of the Projects table
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/language_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/corpus_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$service = filter_input(INPUT_GET, "service");
if ($service == "corporaByLanguage") {
  $source_lang = filter_input(INPUT_GET, "source_lang");
  $target_lang = filter_input(INPUT_GET, "target_lang");
  $mode = filter_input(INPUT_GET, "mode");

  if (isset($source_lang) && isset($target_lang) && isset($mode)){
    $language_dao = new language_dao();

    $target_lang_object = $language_dao->getLangByLangCode($target_lang);

    $corpus_dao = new corpus_dao();
    $corpora_filters = array('active' => 'true', 'target_lang' => $target_lang_object->id, 'evalmode' => $mode);
    
    if ($mode != "FLU" && $mode != "PAR") {
      $source_lang_object = $language_dao->getLangByLangCode($source_lang);
      $corpora_filters['source_lang'] = $source_lang_object->id;
    }

    $corpora = $corpus_dao->getFilteredCorpora($corpora_filters);

    echo json_encode(array("result" => 200, "data" => $corpora));
  } else {
    echo json_encode(array("result" => -1, "message" => "Missing parameters"));
  }
} else if ($service = "ade_description") {
  $corpus_id = filter_input(INPUT_GET, "corpus_id");
  if (isset($corpus_id)) {
    $corpus_dao = new corpus_dao();
    $dtProc = new DatatablesProcessing(new keopsdb());

    if ($corpus_id == "last") {
      $sentences = $dtProc->process(
        array(array("s.id", "id"), array("s.source_text", "source_text"), array("s2.source_text", "target_text"), array("s.type", "type")), 
        "sentences as s 
        left join sentences_pairing as sp on (sp.id_1 = s.id) 
        join sentences as s2 on (sp.id_2 = s2.id)
        ", 
        $_GET,
        "", "s.corpus_id = (select id from corpora order by id desc limit 1)"
      );
    } else {
      $sentences = $dtProc->process(
        array(array("s.id", "id"), array("s.source_text", "source_text"), array("s2.source_text", "target_text"), array("s.type", "type")), 
        "sentences as s 
        left join sentences_pairing as sp on (sp.id_1 = s.id) 
        join sentences as s2 on (sp.id_2 = s2.id)
        ", 
        $_GET,
        "", "s.corpus_id = ?", array($corpus_id)
      );
    }

    echo json_encode($sentences);
  }
}