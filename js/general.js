
$(document).ready(function() {
  // In order to make data tables become responsive at first load
  $(".dataTable").on("init.dt", () => {
    $(window).trigger('resize');
  });

  // In order to make data tables become responsive at first load
  $('a[data-toggle="tab"]').on('shown.bs.tab', () => {
    $(window).trigger('resize');
  });

//$.fn.dataTable.enum( [ 'STARTED', 'PENDING', 'DONE' ] );
//$.fn.dataTable.ext.type.order['customenum-pre'] = function ( d ) {
//  alert("HOLI");
//    switch ( d ) {
//        case 'DONE':    return 1;
//        case 'PENDING': return 2;
//        case 'STARTED':   return 3;
//    }
//    return 0;
//};

/**
 * User tasks table
 */
  var user_tasks_table = $("#user-tasks-table").DataTable({
    columnDefs: [{
        targets: 7,
        className: "actions",
        sortable: false,
        searchable: false,
        responsivePriority: 1,
        render: function (data, type, row) {
          var actions_str = "";
          if (row[5] == "DONE") {
            //actions_str += '<span class="glyphicon glyphicon-play-circle disabled" aria-hidden="true"  title="This task is finished"></span>';
            actions_str += '<li><a href="/sentences/evaluate.php?review=1&task_id=' + row[0] + '" title="See your evaluated sentences"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> See your evaluated sentences</a></li>';
          } else {
            actions_str += '<li><a href="/sentences/evaluate.php?task_id=' + row[0] + '" title="Start / continue the task"><span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span> Start / continue the task</a></li>';
          }
          actions_str += '<li><a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Recap of the task</a></li>';
          actions_str += '<li><a href="mailto:' + row[8] + '" title="Contact project manager"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Contact project manager</a></li>';
          

          return `<div class="btn-group">
                  <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="glyphicon glyphicon glyphicon-cog"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    ${actions_str}
                  </ul>
                </div>
          `;
        }
      },
      {
        targets: 5,
//        type: 'customenum',
     render: function (data, type, row) {
       if (row[4] == null || row[5] == "PENDING")
            return row[5];
          if (row[4] == null || row[5] == "DONE")
            return row[5];
          var completed = (parseInt(row[7]) / parseInt(row[4])) * 100;
          return '<div  title="'+row[7]+' of '+row[4]+' sentences evaluated" class="progress">' +
                  '<div class="progress-bar" role="progressbar" aria-valuenow="' + completed + '"' +
                  'aria-valuemin="0" aria-valuemax="100" style="width:' + completed + '%">' +
                  '<span>' + row[7] + ' of ' + row[4] + '</span></div>' +
                  '</div>';
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
  
  /**
   * Completion chart for a task
   */
  var recapChart = new Chart(document.getElementById("pie-chart"), {
    type: 'pie',
    data: {
      labels: labels_pie_chart,
      datasets: [{
        backgroundColor: ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#3e95cd", "#8e5ea2","#3cba9f","#e2782c", "#4D5360","#20a96a"],
        hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#50aeea", "#c184da", "#46d6b7", "#f38f48", "#616774", "#4bcc91"],
        data: data_pie_chart
      }]
    },
    options: {
      title: {
        display: true,
        text: '# of sentences by type'
      }
    }
  });
  



});
