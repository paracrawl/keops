<?php
/**
 * Class for Feedback objects.
 * Contains the information related to feedback
 * 
 * int id: Feedback ID
 * int score: User review (3 - Awesome, 2 - Good, 1 - Bad)
 * string comments: User comments on the application
 * timestamp created: Date when the feedback was sent
 * int user: ID of the user who sent the feedback
 * int task: ID of the task the user sent the feedback from
 */
class feedback_dto {
  public $id;
  public $score;
  public $comments;
  public $created;
  public $user_id;
  public $task_id;

  public function __construct() {

  }
}