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
         height: '80%', // Adjust height based on data length
         width: '100%'
      },
      labels: pieDataLabels,
      legend: {
         position: 'bottom', // Pindahkan posisi legenda ke kanan
         width: '20%', // Atur lebar legenda menjadi 20% dari ukuran pie chart
         formatter: function(seriesName, opts) {
            return seriesName + ": " + opts.w.globals.series[opts.seriesIndex];
         },
         itemMargin: {
            vertical: 5 // Menambahkan margin vertikal untuk membuat setiap point di baris baru
         }
      },
      responsive: [{
            breakpoint: 480,
            options: {
               chart: {
                  width: 200
               },
               legend: {
                  position: 'bottom',
                  itemMargin: {
                     vertical: 5 // Menambahkan margin vertikal untuk responsive
                  }
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
      plotOptions: {
         bar: {
            horizontal: true,
            columnWidth: "100%",
            borderRadiusApplication: "end",
            borderRadius: 6,
            dataLabels: {
               position: "top",
            },
         },
      },
      colors: ['#FF4560', '#008FFB', '#00E396', '#775DD0'],
      xaxis: {
            categories: barDataLabels,
            labels: {
               style: {
                  fontFamily: "Inter, sans-serif",
                  cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
               }
            }
      },
      yaxis: {
            labels: {
               style: {
                  fontFamily: "Inter, sans-serif",
                  cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
               }
            }
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