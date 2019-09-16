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

$service = filter_input(INPUT_GET, "service");

if ($service == "post") {
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
} else if ($service == "get") {
        $id = filter_input(INPUT_GET, "id");
        $cond = ($id != "ALL") ? "where id = ?" : NULL;
        $condArr = ($id != "ALL") ? array($id) : array();
        $dtProc = new DatatablesProcessing(new keopsdb());
        echo json_encode(
            $dtProc->process(array(
                array("f.id"),
                array("u.name"),
                array("f.task_id"),
                array("f.score"),
                array("f.comments"),
                array("f.user_id")
            ),
            "feedback as f join users as u on (f.user_id = u.id)",
            $_GET,
        null, $cond, $condArr));
} else {
    echo json_encode(array("result" => -1));
}
?>