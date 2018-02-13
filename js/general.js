$(document).ready(function() {
  
  var user_tasks_table = $("#user-tasks-table").DataTable({
    columnDefs: [{
      targets: 7,
      className: "actions",
      searchable: false,
      data: function (row, type, val, meta) {
        return '<a href="/sentences/evaluate.php?task_id=' + row[0] + '" title="Start / continue the task"><span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span></a>';
      }
    }],
    order: [[ 6, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: {
        url: "/tasks/task_user_list.php"
    },
    stateSave: true
  });
  
});

