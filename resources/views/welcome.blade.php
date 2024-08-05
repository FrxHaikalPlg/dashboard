<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <script>
      // On page load or when changing themes, best to add inline in `head` to avoid FOUC
      if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
      } else {
          document.documentElement.classList.remove('dark')
      }
  </script>
</head>
<body class="h-screen flex flex-col">
   <div class="antialiased bg-gray-50 dark:bg-gray-900 flex flex-1">
      
   <!-- Sidebar -->
   <x-sidebar></x-sidebar>
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

   <!-- Hidden elements to store JSON data -->
   <div id="pieDataCounts" class="hidden">@json($pieDataCounts)</div>
   <div id="pieDataLabels" class="hidden">@json($pieDataLabels)</div>
   <div id="barDataCounts" class="hidden">@json($barDataCounts)</div>
   <div id="barDataLabels" class="hidden">@json($barDataLabels)</div>

   <script src="/js/dark-mode.js"></script>
   <script src="/js/chart.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>
</html>