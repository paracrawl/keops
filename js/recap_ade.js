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

          // Table
          for(let i = 20; i < 101; i += 20) {
            let left = i - 20;
            let right = i;
            let acum = 0;
            for(let j = right; (left == 0) ? j >= left : j > left; j -= 10) {
              acum += (j in data.stats) ? data.stats[j] : 0;
            }

            let tr = document.createElement('tr');
            let percentage = document.createElement('td');
            let amount = document.createElement('td');

            $(percentage).html(`${left == 0 ? left : left + 1}% - ${right}%`);
            $(amount).html(acum);

            $(tr).append(percentage);
            $(tr).append(amount);
            $('#table-intra tbody').append(tr);
          }
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
          // Chart
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

          if (document.querySelector('#point-chart-inter')) {
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

            // Table
            for(let i = 20; i < 101; i += 20) {
              let left = i - 20;
              let right = i;
              let acum = 0;
              let acum_others = 0;
              for(let j = right; (left == 0) ? j >= left : j > left; j -= 10) {
                acum += (j in data.stats[task_id]) ? data.stats[task_id][j] : 0;
                acum_others += (j in data.stats['other']) ? data.stats['other'][j] : 0;
              }

              let tr = document.createElement('tr');
              let percentage = document.createElement('td');
              let amount = document.createElement('td');
              let amount_other = document.createElement('td');

              $(percentage).html(`${left == 0 ? left : left + 1}% - ${right}%`);
              $(amount).html(acum);
              $(amount_other).html(acum_others);

              $(tr).append(percentage);
              $(tr).append(amount);
              $(tr).append(amount_other);

              $('#table-inter tbody').append(tr);
            }
          }
        }
      }
    });
  });