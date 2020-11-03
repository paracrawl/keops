<?php
require filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/vendor/autoload.php";

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/comment_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/TSVGenerator.class.php");

use Spatie\Async\Pool;

class SentencesDownload {
    private $mc = null;

    function __construct() {
        $this->mc = new Memcached();
        $this->mc->addServer("localhost", 11211);
    }

    private function generateId() {
        return uniqid(rand(), true);
    }

    private function tempdir() {
        $tempfile=tempnam(sys_get_temp_dir(), 'keops-');
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        if (is_dir($tempfile)) { return $tempfile; }
    }

    public function create_zip($project_id) {
        $uid = $this->generateId();
        $pool = Pool::create();

        $pool->add(function() use ($project_id) {
            $tmpdir = $this->tempdir();
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

            return $zipdir;
        })->then(function ($output) use ($uid) {
            $this->mc->set($uid, $output); 
        });

        $pool->wait();

        return $uid;
    }

    public function get_output($uid) { 
        return $this->mc->get($uid);
    }
}

?>