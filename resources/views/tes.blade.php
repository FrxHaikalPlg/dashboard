<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pie Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    
<div class="max-w-sm w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
    <form action="{{ route('excel.data') }}" method="GET">
        <select name="column" onchange="this.form.submit()">
            @foreach($columns as $key => $value)
                <option value="{{ $key }}" {{ $selectedColumn == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
    </form>
    <div id="pie-chart"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var options = {
        series: @json($dataCounts),
        chart: {
            type: 'pie',
            height: '100%', // Set height to 100% of the container
            width: '100%'  // Set width to 100% of the container
        },
        labels: @json($dataLabels),
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

    var chart = new ApexCharts(document.querySelector("#pie-chart"), options);
    chart.render();

    // Membuat chart draggable dan resizable
    $("#pie-chart").resizable({
        alsoResize: "#pie-chart svg", // Resize the SVG element inside the container
        resize: function(event, ui) {
            chart.updateOptions({
                chart: {
                    width: ui.size.width,
                    height: ui.size.height
                }
            }, false, false, false);
        }
    }).draggable();
});
</script>

<style>
#pie-chart {
    padding: 10px;
    background-color: #fff;
    border: 1px solid #ddd;
}
</style>

</body>
</html>