<?php
/**
 * Class for Task objects.
 * Contains information related to a given task
 * 
 * int id: Task ID
 * int project_id: Project ID (of the project the tasks belongs to)
 * int assigned_user: User ID of the assigned user
 * int corpus_id: Corpus ID of the corpus where the sentences come from
 * int size: Amount of sentences in the task
 * string status: Completion status of the task
 * string creation_date: Date when the task was created
 * string assigned_date: Date when the task was assigned to the user
 * string completed_date: Date when the user completed the task
 */
class task_dto {
  public $id;
  public $project_id;
  public $assigned_user;
  public $corpus_id;
  public $size;
  public $status;
  public $creation_date;
  public $assigned_date;
  public $completed_date;
  public $source_lang;
  public $target_lang;
  public $source_lang_object;
  public $target_lang_object;
  public $mode;
}
