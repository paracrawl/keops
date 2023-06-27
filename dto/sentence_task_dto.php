<?php
/**
 * Class for Sentence Tasks.
 * Contains sentences associated to tasks
 * 
 * map labels: Possible values that can take the evaluation of the sentence
 * int task_id: Task ID
 * int sentence_id: Sentence ID
 * string source_text: Source sentence
 * string target_text: Target sentence
 * string evaluation: Evaluation of the sentence in this given task
 * string creation_date: Creation date of the task
 * string completed_date: Evaluation date of the sentence
 * string comments: Comments provided by the evaluator of this sentence in this task
 */
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
  public $time;
  
  /**
   * Retrieves the text (label) evaluation label of a evaluation type
   * 
   * @param string $value Value of the evaluation
   * @return string Label
   */
  public static function  getLabel($value){
    foreach (sentence_task_dto::$labels as $label) {
      if ($label['value'] == $value){
        return $label['label'];
      }
    }
    return "";
  }

}

/**
 * Map of evaluation values, labels and titles
 */
sentence_task_dto::$labels = array(
  array( 'value' => 'P', 'label' => 'Pending', 'title' => 'Don\'t take the decision now' ),
  array( 'value' => 'L', 'label' => 'Wrong language id.', 'title' => '' ),
  array( 'value' => 'A', 'label' => 'Incorrect alignment', 'title' => '' ),
  array( 'value' => 'T', 'label' => 'Wrong tokenization', 'title' => '' ),
  array( 'value' => 'MT', 'label' => 'MT translation', 'title' => '' ),
  array( 'value' => 'E', 'label' => 'Translation error', 'title' => '' ),
  array( 'value' => 'F', 'label' => 'Free translation', 'title' => '' ),
  array( 'value' => 'V', 'label' => 'Valid translation', 'title' => 'No issues found on parallel sentences' ),


  array( 'value' => 'WL', 'label' => 'Wrong language', 'title' => ''),
  array( 'value' => 'ML', 'label' => 'Mixed language', 'title' => ''),
  array( 'value' => 'MC', 'label' => 'Missing content', 'title' => ''),
  array( 'value' => 'RC', 'label' => 'Replaced content', 'title' => ''),
  array( 'value' => 'MA', 'label' => 'Misalignment', 'title' => ''),
  array( 'value' => 'LQT', 'label' => 'Low quality translation', 'title' => ''),
  array( 'value' => 'CBT', 'label' => 'Correct boilerplate translation', 'title' => '')
);

