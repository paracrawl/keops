
<div class="modal fade" id="popup_remove_task" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content ">
      <div class="modal-header alert-warning">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Beware!</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to proceed with the removal of the task <b><span id="modal-task-id"></span></b>, assigned to the user <b><span id="modal-username"></span></b>?
        <br>
        Please note that <b> existing evaluation results for this task will be lost</b> and this operation cannot be undone. 
      </div>
      <div class="modal-footer">
        <form action="/tasks/task_update.php" role="form" method="post">
          <input type="hidden" name="id" id="task_id" value="">
          <input type="hidden" name="action" id="action" value="remove">

          <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Remove</button>
        </form>
      </div>
    </div>
  </div>
</div>