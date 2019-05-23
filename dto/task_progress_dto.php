<?php
/**
 * Class for Task Progress objects
 * Contains the information about the completion status of a task
 * 
 * int current: Current sentence
 * int total: Total sentences
 * int completed: Completed (evaluated) sentences
 */
class task_progress_dto {
  public $current;
  public $total;
  public $completed; 
}