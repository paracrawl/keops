<?php
/**
 * Class for Comment objects.
 * Contains the information about a comment
 * in evaluation.
 * 
 * int pair: ID of the pair of sentences the comment belongs to
 * string name: ID of the comment
 * string value: The comment itself
 */
class comment_dto {
  public $pair;
  public $name;
  public $value;

  public function __construct() {

  }

  /**
   * Builds a new comment object
   * 
   * @param int $pair ID of the pair of sentences the comment belongs to Corpus ID
   * @param string $name ID of the comment
   * @param int $value The comment itself
   * @return \self
   */
  public function newComment($pair, $name, $value) {
    $instance = new self();
    $instance->pair = $pair;
    $instance->name = $name;
    $instance->value = $value;
    return $instance;
  }
}