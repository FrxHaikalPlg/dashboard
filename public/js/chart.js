var pieChart, barChart, generationChart; // Mendefinisikan variabel di luar untuk akses global

document.addEventListener('DOMContentLoaded', function () {
   var pieDataCounts = JSON.parse(document.getElementById('jenisKelaminCounts').textContent);
   var pieDataLabels = JSON.parse(document.getElementById('jenisKelaminLabels').textContent);
   var barDataCounts = JSON.parse(document.getElementById('barDataCounts').textContent);
   var barDataLabels = JSON.parse(document.getElementById('barDataLabels').textContent);
   var generationData = JSON.parse(document.getElementById('generationData').textContent);
   var generationLabels = JSON.parse(document.getElementById('generationLabels').textContent);

   // Filter out labels and data with zero values for generation chart
   var filteredGenerationData = [];
   var filteredGenerationLabels = [];
   for (var i = 0; i < generationData.length; i++) {
      if (generationData[i] > 0) {
         filteredGenerationData.push(generationData[i]);
         filteredGenerationLabels.push(generationLabels[i]);
      }
   }

   // Function to get chart height based on screen width
   function getChartHeight() {
      if (window.innerWidth >= 768 && window.innerWidth < 1024) {
         return '100%';
      }
      return '75%';
   }

   // Chart Jenis Kelamin
   var pieOptions = {
      series: pieDataCounts,
      chart: {
         type: 'pie',
         height: '80%', // Adjust height based on data length
         width: '100%'
      },
      stroke: {
         colors: ["white"],
         lineCap: "",
      },
      plotOptions: {
         pie: {
           labels: {
             show: true,
           },
           size: "100%",
           dataLabels: {
             offset: -25
           }
         },
       },
      labels: pieDataLabels,
      dataLabels: {
         enabled: true,
         style: {
         fontFamily: "Inter, sans-serif",
         },
      },
      colors: ['#E74694', '#6875F5'], // Warna untuk Wanita (pink) dan Laki-laki (biru)
      legend: {
         position: "bottom",
         fontFamily: "Inter, sans-serif",
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

   // Bar Chart
   var barOptions = {
      series: [{
            name: 'Data',
            data: barDataCounts
      }],
      chart: {
         sparkline: {
           enabled: false,
         },
         type: "bar",
         width: "95%",
         height: getChartHeight(), // Set initial height
         toolbar: {
           show: false,
         }
       },
       fill: {
         opacity: 1,
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
       legend: {
         show: true,
         position: "bottom",
       },
       dataLabels: {
         enabled: true,
          offsetX: -6,
          style: {
            fontSize: '12px',
            colors: ['#fff']
          },
       },
      //colors: [''],
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
      grid: {
         show: true,
         strokeDashArray: 4,
         padding: {
           left: 2,
           right: 2,
           top: -20
         },
       },
       fill: {
         opacity: 1,
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

   var generationOptions = {
      series: filteredGenerationData,
      colors: ['#FEB019', '#00E396', '#FF4560', '#775DD0'], // Warna untuk Gen X (kuning), Gen Y (hijau), Gen Z (merah), dan Baby Boomer (ungu)
      chart: {
         type: 'pie',
         height: '80%', // Adjust height based on data length
         width: '100%'
      },
       stroke: {
         colors: ["white"],
         lineCap: "",
       },
       plotOptions: {
         pie: {
           labels: {
             show: true,
           },
           size: "100%",
           dataLabels: {
             offset: -25
           }
         },
       },
      labels: filteredGenerationLabels,
      dataLabels: {
         enabled: true,
         style: {
           fontFamily: "Inter, sans-serif",
         },
       },
      legend: {
         position: "bottom",
         fontFamily: "Inter, sans-serif",
         formatter: function(seriesName, opts) {
            // Hanya tampilkan legend yang memiliki data
            if (opts.w.globals.series[opts.seriesIndex] > 0) {
               return seriesName + ": " + opts.w.globals.series[opts.seriesIndex];
            }
            return null;
         },
         itemMargin: {
            vertical: 5 // Menambahkan margin vertikal untuk responsive
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

   generationChart = new ApexCharts(document.querySelector("#generationChart"), generationOptions);
   generationChart.render();

   // Update chart height on window resize
   window.addEventListener('resize', function() {
      var newHeight = getChartHeight();
      barChart.updateOptions({ chart: { height: newHeight } });
   });
});

document.getElementById('bar_column').addEventListener('change', function() {
   updateChartData('bar');
});

function updateChartData(chartType) {
   var barColumn = document.getElementById('bar_column').value;

   fetch(`/api/chart-data?bar_column=${barColumn}`)
      .then(response => response.json())
      .then(data => {
            if (chartType === 'bar') {
               barChart.updateOptions({
                  series: [{ data: data.barDataCounts }],
                  xaxis: { categories: data.barDataLabels }
               });
            }
      });
}