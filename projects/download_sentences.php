<?php
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/sentences_download.class.php");

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$project_id = filter_input(INPUT_GET, "project_id");
$status = filter_input(INPUT_GET, "status");
$file = filter_input(INPUT_GET, "file");

$sentences_download = new SentencesDownload();
if (isset($project_id)) {
    $uid = $sentences_download->create_zip($project_id);
    echo $uid;
} else if (isset($status)) {
    $output = $sentences_download->get_output($status);
    echo ($output) ? $output : "-1";
} else if (isset($file)) {
    $output = $sentences_download->get_output($file);
    error_log("file " . $output);
    $file_name = basename($output);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Length: " . filesize($output));
    readfile($output);
    exit;
}
?>
