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
      
   <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
         <div class="rounded-lg  h-auto md:h-auto flex flex-col max-h-96 bg-white dark:bg-gray-800 overflow-x-auto">
            <form action="{{ route('excel.data') }}" method="GET" class="p-4">
               <div>
                  <select name="bar_column" id="bar_column" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                     @foreach($columns as $key => $value)
                        <option value="{{ $key }}" {{ $selectedBarColumn == $key ? 'selected' : '' }}>{{ $value }}</option>
                     @endforeach
                  </select>
                  <input type="hidden" name="pie_column" value="{{ $selectedPieColumn }}">
               </div>
            </form>
            <div id="bar-chart" class="flex-1 m-0 p-0 relative w-auto"></div>
         </div>
         
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
         <div class="rounded-lg h-64 md:h-96 flex flex-col bg-white dark:bg-gray-800">
            <form action="{{ route('excel.data') }}" method="GET" class="p-4">
               <div>
                  <select name="pie_column" id="pie_column" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                     @foreach($columns as $key => $value)
                        <option value="{{ $key }}" {{ $selectedPieColumn == $key ? 'selected' : '' }}>{{ strlen($value) > 15 ? substr($value, 0, 15) . '...' : $value }}</option>
                     @endforeach
                  </select>
                  <input type="hidden" name="bar_column" value="{{ $selectedBarColumn }}">
               </div>
            </form>
            <div id="pie-chart" class="flex-1 m-0 p-0 relative"></div>
         </div>
         <div class="rounded-lg h-64 md:h-96 flex flex-col max-h-96 bg-white dark:bg-gray-800">
            <div class="flex justify-center items-center p-4">
               <div class="flex justify-center items-center mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 sm:text-sm rounded-md bg-white text-gray-900 dark:bg-gray-700 dark:placeholder-gray-400 dark:text-white">
                  <h5 class="text-xl leading-none text-gray-900 dark:text-white pe-1">Generasi</h5>
               </div>   
            </div>
            <div id="generationChart" class="flex-1 m-0 p-0 relative"></div>
         </div>
      </div>
   </div>

      <div class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-96 mb-4 bg-white dark:bg-gray-800"></div>
   </main>
   

   <!-- Hidden elements to store JSON data -->
   <div id="pieDataCounts" class="hidden">@json($pieDataCounts)</div>
   <div id="pieDataLabels" class="hidden">@json($pieDataLabels)</div>
   <div id="barDataCounts" class="hidden">@json($barDataCounts)</div>
   <div id="barDataLabels" class="hidden">@json($barDataLabels)</div>
   <div id="generationData" class="hidden">@json(array_values($generations))</div>
   <div id="generationLabels" class="hidden">@json(array_keys($generations))</div>

   <!-- Modal -->
   <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
      <div class="relative p-4 w-full max-w-md max-h-full">
         <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
               <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
               </svg>
               <span class="sr-only">Close modal</span>
            </button>

            <div class="p-4 md:p-5 text-center">
               <form action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
                  <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file" name="file">
                  <button type="submit" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">Upload</button>
               </form>
            </div>
         </div>
      </div>
   </div>

   <script src="/js/dark-mode.js"></script>
   <script src="/js/chart.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>
</html>