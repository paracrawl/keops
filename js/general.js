
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
    columnDefs: [
      {
        targets: 2,
        render: function (data, type, row) {
          return (row[2]) ? row[2] : '—';
        },
        createdCell: function(td, cellData, rowData, row, col) {
          td.setAttribute('title', (row[2]) ? '' : 'This task has no source language because it evaluates fluency');
        }
      },
      {
        targets: 7,
        searchable: true,
        sortable: true,
        render: function (data, type, row) {
          switch (row[7]) {
            case 'VAL':
              return 'Validation';
            case 'ADE':
              return 'Adequacy';
            case 'FLU':
              return 'Fluency';
            case 'RAN':
              return 'Ranking';
          }
        }
      },
      {
        targets: 8,
        searchable: false,
        sortable: false,
        responsivePriority: 1,
        className: "actions",
        render: function (data, type, row) {
          let actions_str = '';

          if (row[5] == "DONE") {
            actions_str += '<a href="/sentences/evaluate.php?review=1&task_id=' + row[0] + '" title="See your evaluated sentences"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span></a>';
          } else {
            actions_str += '<a href="/sentences/evaluate.php?task_id=' + row[0] + '" title="Start / continue the task"><span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span></a>';
          }

          return actions_str;
        }
      },
      {
        targets: 9,
        className: "actions",
        sortable: false,
        searchable: false,
        responsivePriority: 1,
        render: function (data, type, row) {
          var actions_str = "";
          actions_str += '<li><a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Recap of the task</a></li>';
          actions_str += '<li><a href="/contact.php?p='+ row[9] + '" title="Contact project manager"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Contact project manager</a></li>';

          return `<div class="btn-group">
                  <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-label="Toggle actions" aria-haspopup="true" aria-expanded="false">
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
        render: function (data, type, row) {
          if (row[4] == null || row[5] == "PENDING")
            return row[5];
          if (row[4] == null || row[5] == "DONE")
            return row[5];
          
          var completed = (parseInt(row[8]) / parseInt(row[4])) * 100;
          
          return '<div  title="'+row[8]+' of '+row[4]+' sentences evaluated" class="progress">' +
                  '<div class="progress-bar" role="progressbar" aria-valuenow="' + completed + '"' +
                  'aria-valuemin="0" aria-valuemax="100" style="width:' + completed + '%">' +
                  '<span>' + row[8] + ' of ' + row[4] + '</span></div>' +
                '</div>';
        }
      },
      {
        targets: 6,
        render: function (data, type, row) {
          return formatDate(row[6]);
        }
      }
    ],
    order: [[ 6, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: {
    url: "/tasks/task_user_list.php"
    },
    stateSave: true
  });
  
  function rainbow(numOfSteps, step) {
    // This function generates vibrant, "evenly spaced" colours (i.e. no clustering). This is ideal for creating easily distinguishable vibrant markers in Google Maps and other apps.
    // Adam Cole, 2011-Sept-14
    // HSV to RBG adapted from: http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
    var r, g, b;
    var h = step / numOfSteps;
    var i = ~~(h * 6);
    var f = h * 6 - i;
    var q = 1 - f;
    switch(i % 6){
        case 0: r = 1; g = f; b = 0; break;
        case 1: r = q; g = 1; b = 0; break;
        case 2: r = 0; g = 1; b = f; break;
        case 3: r = 0; g = q; b = 1; break;
        case 4: r = f; g = 0; b = 1; break;
        case 5: r = 1; g = 0; b = q; break;
    }

    var c = "#" + ("00" + (~ ~(r * 255)).toString(16)).slice(-2) + ("00" + (~ ~(g * 100)).toString(16)).slice(-2) + ("00" + (~ ~(b * 255)).toString(16)).slice(-2);
    return (c);
}

  /**
   * Completion chart for a task
   */
  let colors = ["#21b00b"];
  for (let i = 0; i < labels_pie_chart.length - 1; i++) {
    colors.push(rainbow(labels_pie_chart.length, ((i > 1) ? (i + 1) : i)))
  }

  if (document.getElementById("pie-chart") && labels_pie_chart && data_pie_chart) {
    var recapChart = new Chart(document.getElementById("pie-chart"), {
      type: 'pie',
      data: {
        labels: labels_pie_chart,
        datasets: [{
          backgroundColor: colors,
          hoverBackgroundColor: colors,
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
  }
});

function formatDate(datestring) {
  let date = new Date(datestring);
  if (datestring == null || isNaN(date.getTime())) return "—";

  let day = ((date.getDate() < 10) ? "0" + date.getDate() : date.getDate());
  let month = ((date.getMonth() + 1 < 10) ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1));
  return `${day}.${month}.${date.getFullYear()}`;
}