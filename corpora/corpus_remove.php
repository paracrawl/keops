<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $corpus_id = filter_input(INPUT_GET, "id");
  if (!isset($corpus_id)) {
    header("Location: /admin/index.php#corpora");
    die();
  }
  $corpus_dao = new corpus_dao();
  $corpus = $corpus_dao->getCorpusById($corpus_id);
//  $task_dao  = new task_dao();
// $tasks = $task_dao->getTasksByCorpus($corpus_id);
//  $language_dao = new language_dao();
//  $languages = $language_dao->getLanguages();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Remove corpus</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#corpora">Corpora</a></li>
        <li><a href="/corpora/corpus_preview.php?id=<?php echo $corpus->id; ?>"><?= $corpus->name?></a></li>
        <li class="active">Remove corpus</li> 
      </ul>
      <div class="page-header">
        <h1>Remove corpus</h1>
      </div>
      <div class="alert alert-warning" role="alert">
        <h3>BEWARE!</h3>
        <br>
        Removing the corpus <?=$corpus->name?> will result in the removal of the tasks listed below. Are sure you want to proceed? Please note that this operation <b>cannot be undone</b>. <br>         
        <br>
        <b>Tip:</b>
        If you just want to prevent this corpus to be used for new tasks, not removing the already existing ones, mark it as inactive <a href="/corpora/corpus_edit.php?id=<?=$corpus->id; ?>">here</a>.
      </div>

      <table id="corpus-tasks-table" class="table table-striped table-bordered" data-corpusid="<?= $corpus->id ?>" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Assigned user</th>
            <th>Size</th>
            <th>Corpus</th>  
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Assigned user</th>
            <th>Size</th>
            <th>Corpus</th>  
            <th>Status</th>
            <th>Creation date</th>
            <th>Assigned date</th>
            <th>Completed date</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>

      <form action="/corpora/corpus_update.php" role="form" method="post">
        <input type="hidden" name="id" id="id" value="<?= $corpus->id ?>">
        <input type="hidden" name="action" id="action" value="remove">
        <div class="form-group">
          <div class="col-sm-4 text-right">
            <a href="/corpora/corpus_preview.php?id=<?php echo $corpus->id;?>" class="col-sm-offset-1 btn btn-info" tabindex="6">Cancel</a>
            <button type="submit" class="col-sm-offset-1 btn btn-danger" tabindex="5">Remove corpus</button>
          </div>
        </div>
      </form>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>