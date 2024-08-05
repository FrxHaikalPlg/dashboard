<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pie Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
</head>
<body>
    
<div class="max-w-sm w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
    <form action="{{ route('excel.data') }}" method="GET">
        <div>
            <label for="pie_column">Pie Chart Column:</label>
            <select name="pie_column" id="pie_column" onchange="this.form.submit()">
                @foreach($columns as $key => $value)
                    <option value="{{ $key }}" {{ $selectedPieColumn == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="bar_column">Bar Chart Column:</label>
            <select name="bar_column" id="bar_column" onchange="this.form.submit()">
                @foreach($columns as $key => $value)
                    <option value="{{ $key }}" {{ $selectedBarColumn == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </form>
    <div id="pie-chart"></div>
    <div id="bar-chart"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var pieDataCounts = @json($pieDataCounts);
    var pieDataLabels = @json($pieDataLabels);
    var barDataCounts = @json($barDataCounts);
    var barDataLabels = @json($barDataLabels);

    var pieOptions = {
        series: pieDataCounts,
        chart: {
            type: 'pie',
            height: '100%',
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
            height: '100%',
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

    var pieChart = new ApexCharts(document.querySelector("#pie-chart"), pieOptions);
    pieChart.render();

    var barChart = new ApexCharts(document.querySelector("#bar-chart"), barOptions);
    barChart.render();
});
</script>

<style>
#pie-chart, #bar-chart {
    padding: 10px;
    background-color: #fff;
    border: 1px solid #ddd;
    margin-top: 20px;
}
</style>

</body>
</html>