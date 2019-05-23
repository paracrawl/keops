<?php
/**
 * Class for Task Stats objects
 * Contains the information about the evaluation results
 * 
 * int total: Total amount of sentences
 * array array_type: Array containing the amount of evaluations for each evaluation type
 */
class task_stats_dto {
  public $total;
  public $array_type;
  
  public function __construct(){
    $this->total = 0;
    $this->array_type = array();
  }
}