<?php
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/comment_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/TSVGenerator.class.php");

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$project_id = filter_input(INPUT_GET, "project_id");

function tempdir() {
    $tempfile=tempnam(sys_get_temp_dir(), 'keops-');
    if (file_exists($tempfile)) { unlink($tempfile); }
    mkdir($tempfile);
    if (is_dir($tempfile)) { return $tempfile; }
}

if (isset($project_id)) {
    $tmpdir = tempdir();
    $task_dao = new task_dao();
    $generator = new TSVGenerator();

    $tsv_list = array();
    foreach($task_dao->getTasksByProject($project_id) as $task) {
        $task_id = $task->id;
        $tsv_file = "$tmpdir/$task_id.tsv";

        $generator->generate_annotated($task_id, $tsv_file);
        $tsv_list[] = $tsv_file;
    }

    $zipdir = "$tmpdir/sentences-$project_id.zip";
    $zip = new ZipArchive;

    if ($zip->open($zipdir, ZipArchive::CREATE) == TRUE) {
        foreach ($tsv_list as $tsv_file) {
            $tsv_name = basename($tsv_file);
            $zip->addFile($tsv_file, "/$tsv_name");
        }

        $zip->close();
    }

    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=" . basename($zipdir));
    header("Content-Length: " . filesize($zipdir));
    readfile($zipdir);
    exit;
}
?>
