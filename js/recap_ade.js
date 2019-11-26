$(document).ready(function() {
    let task_id = $("#task_id").val();

    $.ajax({
      method: "POST",
      url: "/services/recap.php",
      data: {
        service: "stats_ade",
        type: "intra",
        task_id: task_id
      },
      success: (data_raw) => {
        let data = JSON.parse(data_raw);
        if (data.result == 200) {
          let labels = []; for(let i = 0; i < 101; i += 10) labels.push(`${i}%`);
          let graphdata = [];

          for(let i = 0; i < 101; i += 10) {
            graphdata.push((i in data.stats) ? data.stats[i] : 0);
          }

          let ctx = document.querySelector('#point-chart-intra').getContext('2d');
          let chart = new Chart(ctx, {
              type: 'line',
              data: {
                labels: labels,
                datasets: [{
                  backgroundColor: 'rgba(0, 74, 122, .5)',
                  data: graphdata
                }]
              },
              options: {
                legend: {
                    display: false
                }
              }
          });
        }
      }
    });

    $.ajax({
      method: "POST",
      url: "/services/recap.php",
      data: {
        service: "stats_ade",
        type: "inter",
        task_id: task_id,
        mode: "ADE"
      },
      success: (data_raw) => {
        let data = JSON.parse(data_raw);
        if (data.result == 200) {
          let labels = []; for(let i = 0; i < 101; i += 10) labels.push(`${i}%`);
          let datasets = [];

          let dataset = { label: `Task ${task_id}`, data: [] };
          dataset.backgroundColor = "rgba(0, 74, 122, .5)";
          for(let i = 0; i < 101; i += 10) {
            dataset.data.push((i in data.stats[task_id]) ? data.stats[task_id][i] : 0);
          }

          datasets.push(dataset);

          dataset = { label: `Others`, data: [] };
          for(let i = 0; i < 101; i += 10) {
            dataset.data.push((i in data.stats['other']) ? data.stats['other'][i] : 0);
          }

          datasets.push(dataset);

          let ctx = document.querySelector('#point-chart-inter').getContext('2d');
          let chart = new Chart(ctx, {
              type: 'line',
              data: {
                labels: labels,
                datasets: datasets
              },
              options: {
                scales: {
                  yAxes: [{
                     ticks: {
                        stepSize: 5
                     }
                  }]
                }
              }
          });
        }
      }
    });
  });