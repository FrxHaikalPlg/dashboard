var pieChart, barChart; // Mendefinisikan variabel di luar untuk akses global

document.addEventListener('DOMContentLoaded', function () {
   var pieDataCounts = JSON.parse(document.getElementById('pieDataCounts').textContent);
   var pieDataLabels = JSON.parse(document.getElementById('pieDataLabels').textContent);
   var barDataCounts = JSON.parse(document.getElementById('barDataCounts').textContent);
   var barDataLabels = JSON.parse(document.getElementById('barDataLabels').textContent);

   var pieOptions = {
      series: pieDataCounts,
      chart: {
            type: 'pie',
            height: '90%',
            width: '100%'
      },
      labels: pieDataLabels,
      responsive: [{
            breakpoint: 480,
            options: {
               chart: {
                  width: 200
               },
               legend: {
                  position: 'bottom'
               }
            }
      }]
   };

   var barOptions = {
      series: [{
            name: 'Data',
            data: barDataCounts
      }],
      chart: {
            type: 'bar',
            height: '80%',
            width: '100%'
      },
      colors: ['#FF4560', '#008FFB', '#00E396', '#775DD0'],
      xaxis: {
            categories: barDataLabels
      },
      responsive: [{
            breakpoint: 480,
            options: {
               chart: {
                  width: 200
               },
               legend: {
                  position: 'bottom'
               }
            }
      }]
   };

   pieChart = new ApexCharts(document.querySelector("#pie-chart"), pieOptions);
   pieChart.render();

   barChart = new ApexCharts(document.querySelector("#bar-chart"), barOptions);
   barChart.render();
});

document.getElementById('pie_column').addEventListener('change', function() {
   updateChartData('pie');
});

document.getElementById('bar_column').addEventListener('change', function() {
   updateChartData('bar');
});

function updateChartData(chartType) {
   var pieColumn = document.getElementById('pie_column').value;
   var barColumn = document.getElementById('bar_column').value;

   fetch(`/api/chart-data?pie_column=${pieColumn}&bar_column=${barColumn}`)
      .then(response => response.json())
      .then(data => {
            if (chartType === 'pie') {
               pieChart.updateOptions({
                  series: data.pieDataCounts,
                  labels: data.pieDataLabels
               });
            } else {
               barChart.updateOptions({
                  series: [{ data: data.barDataCounts }],
                  xaxis: { categories: data.barDataLabels }
               });
            }
      });
}
