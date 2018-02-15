$(document).ready(function() {
  
  var user_tasks_table = $("#user-tasks-table").DataTable({
    columnDefs: [{
      targets: 7,
      className: "actions",
      searchable: false,
      data: function (row, type, val, meta) {
        return '<a href="/sentences/evaluate.php?task_id=' + row[0] + '" title="Start / continue the task"><span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span></a>' +
                '<a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span></a>';
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

