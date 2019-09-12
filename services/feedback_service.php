<?php
/**
 * Project services.
 * Currently "new", that creates a new project and redirects to the Projects tab,
 *  and "list_dt", that serves the datatables content of the Projects table
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/feedback_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/feedback_dao.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$score = filter_input(INPUT_POST, "feedback_score");
$comments = filter_input(INPUT_POST, "feedback_comments", FILTER_SANITIZE_STRING);
$task = filter_input(INPUT_POST, "feedback_task_id");

if (isset($score)) {
    $feedback_dto = new feedback_dto();
    $feedback_dto->score = $score;
    $feedback_dto->comments = $comments;
    $feedback_dto->user_id = getUserId();
    $feedback_dto->task_id = $task;

    $feedback_dao = new feedback_dao();
    $feedback_dao->insertFeedback($feedback_dto);

    echo json_encode(array("result" => 200));
} else {
    echo json_encode(array("result" => -1));
}
?>