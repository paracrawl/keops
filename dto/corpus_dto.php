<?php

class corpus_dto {
  public $id;
  public $name;
  public $source_lang;
  public $target_lang;
  public $lines;
  public $creation_date;
  public $active;

  public function __construct() {

  }

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