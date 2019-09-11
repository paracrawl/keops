<?php

/**
 * Class for Sentences.
 * Contains the information for  a sentence
 * 
 * int id: Sentence ID
 * int corpus_id: ID of the corpus the sentence belongs to
 * string source_text: Source sentence
 * string target_text: Target sentence
 */
class sentence_dto {
  public $id;
  public $corpus_id;
  public $source_text;
  public $target_text;
  public $type;
  public $system;
}