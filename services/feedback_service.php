<?php
/**
 * Project services.
 * Currently "new", that creates a new project and redirects to the Projects tab,
 *  and "list_dt", that serves the datatables content of the Projects table
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$score = filter_input(INPUT_POST, "feedback_score");
$comments = filter_input(INPUT_POST, "feedback_comments", FILTER_SANITIZE_STRING);

if (isset($score)) {
    echo json_encode(array("result" => 200));
} else {
    echo json_encode(array("result" => -1));
}
?>