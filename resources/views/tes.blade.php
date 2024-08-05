<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generations Pie Chart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Include ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    
<!-- Pie Chart Container -->
<div id="generationChart" style="height: 400px;"></div>

<script>
    var options = {
        series: @json(array_values($generations)),
        chart: {
            type: 'pie',
            height: 400
        },
        labels: @json(array_keys($generations)),
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

    var chart = new ApexCharts(document.querySelector("#generationChart"), options);
    chart.render();
</script>

</body>
</html>