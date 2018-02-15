<?php

class sentence_task_dto {
  public static $labels;
  
  public $id;
  public $task_id;
  public $sentence_id;
  public $source_text;
  public $target_text;
  public $evaluation;
  public $creation_date;
  public $completed_date;
  public $comments;
}
sentence_task_dto::$labels = array(
    array( 'value' => 'L', 'label' => 'Wrong language identification', 'title' => '' ),
    array( 'value' => 'A', 'label' => 'Incorrect alignment', 'title' => '' ),
    array( 'value' => 'T', 'label' => 'Wrong tokenization', 'title' => '' ),
    array( 'value' => 'MT', 'label' => 'MT translation', 'title' => '' ),
    array( 'value' => 'E', 'label' => 'Translation error', 'title' => '' ),
    array( 'value' => 'F', 'label' => 'Free translation', 'title' => '' ),
    array( 'value' => 'V', 'label' => 'Valid translation', 'title' => 'No issues found on parallel sentences' ),
    array( 'value' => 'P', 'label' => 'Pending', 'title' => 'Don\'t take the decision now' )
);