<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="h-screen flex flex-col">
    
<div class="antialiased bg-gray-50 dark:bg-gray-900 flex flex-1">
<!-- Sidebar -->

<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
         <li>
            <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                  <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                  <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
               </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="#" class="flex items-center p-2 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
               <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                  <path d="M16 14V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v15a3 3 0 0 0 3 3h12a1 1 0 0 0 0-2h-1v-2a2 2 0 0 0 2-2ZM4 2h2v12H4V2Zm8 16H3a1 1 0 0 1 0-2h9v2Z"></path>
               </svg>
               <span class="ms-3">Input Data</span>
            </a>
         </li>
         <li>
            <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example" aria-expanded="false">
                  <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
                  </svg>
                  <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Kota</span>
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"></path>
                  </svg>
            </button>
            <ul id="dropdown-example" class="py-2 space-y-2 hidden">
                  <li>
                     <a href="#" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Products</a>
                  </li>
                  <li>
                     <a href="#" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Billing</a>
                  </li>
                  <li>
                     <a href="#" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Invoice</a>
                  </li>
            </ul>
         </li>
      </ul>
   </div>
</aside>

<!-- Content -->
<main class="flex-1 p-4 md:ml-64 h-auto pt-10 overflow-y-auto">
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
      <div class="border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600 h-64 md:h-96 flex flex-col max-h-96">
         <form action="{{ route('excel.data') }}" method="GET" class="p-4">
            <div>
               <label for="pie_column">Pie Chart Column:</label>
               <select name="pie_column" id="pie_column">
                  @foreach($columns as $key => $value)
                     <option value="{{ $key }}" {{ $selectedPieColumn == $key ? 'selected' : '' }}>{{ $value }}</option>
                  @endforeach
               </select>
               <input type="hidden" name="bar_column" value="{{ $selectedBarColumn }}">
            </div>
         </form>
         <div id="pie-chart" class="flex-1 overflow-hidden m-0 p-0 relative"></div>
      </div>
      <div class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-64 md:h-96 flex flex-col max-h-96">
         <form action="{{ route('excel.data') }}" method="GET" class="p-4">
            <div>
               <label for="bar_column">Bar Chart Column:</label>
               <select name="bar_column" id="bar_column">
                  @foreach($columns as $key => $value)
                     <option value="{{ $key }}" {{ $selectedBarColumn == $key ? 'selected' : '' }}>{{ $value }}</option>
                  @endforeach
               </select>
               <input type="hidden" name="pie_column" value="{{ $selectedPieColumn }}">
            </div>
         </form>
         <div id="bar-chart" class="flex-1 overflow-hidden m-0 p-0 relative"></div>
      </div>
      <div class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-64 md:h-96 flex flex-col max-h-96"></div>
      <div class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-64 md:h-96 flex flex-col max-h-96"></div>
   </div>
   <div class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-96 mb-4"></div>
</main>
</div>

<script>
var pieChart, barChart; // Mendefinisikan variabel di luar untuk akses global

document.addEventListener('DOMContentLoaded', function () {
    var pieDataCounts = @json($pieDataCounts);
    var pieDataLabels = @json($pieDataLabels);
    var barDataCounts = @json($barDataCounts);
    var barDataLabels = @json($barDataLabels);

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
</script>

<style>
#pie-chart, #bar-chart {
    padding: 0; /* Ensure no padding */
    margin: 0; /* Ensure no margin */
    background-color: #fff;
    border: 1px solid #ddd;
    height: 100%; /* Ensure the charts take full height of their container */
    max-height: 100%; /* Ensure the charts do not exceed the container height */
    overflow: hidden; /* Ensure the charts do not overflow */
    position: relative; /* Ensure the charts are positioned correctly */
}

<script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>
</html>