<?php
/**
 * Class for Corpus objects.
 * Contains the information related to a corpus
 * 
 * int id: Corpus ID
 * string name: Corpus name
 * int source_lang: Corpus source language ID
 * int target_lang: Corpus target language ID
 * int lines: Amount of lines of the corpus
 * string creation_date: Upload date
 * boolean active: Corpus status (active/deactivated)
 */
class corpus_dto {
  public $id;
  public $name;
  public $source_lang;
  public $target_lang;
  public $lines;
  public $creation_date;
  public $active;
  public $mode;
  public $added_by;

  public function __construct() {

  }

  /**
   * Builds a new corpus object
   * 
   * @param inte $id Corpus ID
   * @param string $name Corpus name
   * @param int $source_lang Source language ID
   * @param int $target_lang Target language ID
   * @param boolean $active Active status
   * @return \self
   */
  public function newCorpus($id, $name, $source_lang, $target_lang, $active) {
    $instance = new self();
    $instance->id = $id;
    $instance->name = $name;
    $instance->source_lang = $source_lang;
    $instance->target_lang = $target_lang;
    $instance->active = $active;
    return $instance;
  }

}