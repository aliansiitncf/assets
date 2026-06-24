<div class="space-y-6">

  <!-- Header -->
  <div>
    <h1 class="text-2xl md:text-3xl font-bold">
      Dashboard Overview
    </h1>
    <p class="text-base-content/60">Monitoring data assets kamu</p>
  </div>

 <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    <!-- Assets -->
    <div class="bg-base-100 rounded-2xl shadow-md hover:shadow-xl transition hover:-translate-y-1 p-5 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500">Assets</p>
          <h2 class="text-3xl font-bold text-orange-500 mt-1">{{ $assetCount }}</h2>
        </div>
        <div class="bg-orange-100 p-3 rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Categories -->
    <div class="bg-base-100 rounded-2xl shadow-md hover:shadow-xl transition hover:-translate-y-1 p-5 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500">Categories</p>
          <h2 class="text-3xl font-bold text-blue-500 mt-1">{{ $categoryCount }}</h2>
        </div>
        <div class="bg-blue-100 p-3 rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Locations -->
    <div class="bg-base-100 rounded-2xl shadow-md hover:shadow-xl transition hover:-translate-y-1 p-5 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500">Locations</p>
          <h2 class="text-3xl font-bold text-red-500 mt-1">{{ $locationCount }}</h2>
        </div>
        <div class="bg-red-100 p-3 rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Users -->
    <div class="bg-base-100 rounded-2xl shadow-md hover:shadow-xl transition hover:-translate-y-1 p-5 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500">Users</p>
          <h2 class="text-3xl font-bold text-green-500 mt-1">{{ $userCount }}</h2>
        </div>
        <div class="bg-green-100 p-3 rounded-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0" />
          </svg>
        </div>
      </div>
    </div>

  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

    <div class="card bg-base-100 shadow-lg">
      <div class="card-body">
        <h2 class="card-title">Asset per Kategori</h2>
        <canvas id="categoryChart"></canvas>
      </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
      <div class="card-body">
        <h2 class="card-title">Asset per Lokasi</h2>
        <canvas id="locationChart"></canvas>
      </div>
    </div>

  </div>

</div>

<script>
  document.addEventListener('livewire:init', () => {

    const dataChartCategory = @json($chartCategory);
    const dataChartLocation = @json($chartLocation);

    const labelCategory = dataChartCategory.map(item => item.name);
    const valueCategory = dataChartCategory.map(item => item.value);

    const labelLocation = dataChartLocation.map(item => item.name);
    const valueLocation = dataChartLocation.map(item => item.value);

    // Chart Category
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: labelCategory,
            datasets: [{
                label: 'Assets',
                data: valueCategory,
                backgroundColor: '#facc15', // bumblebee (yellow)
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false }},
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // Chart Location
    new Chart(document.getElementById('locationChart'), {
        type: 'bar',
        data: {
            labels: labelLocation,
            datasets: [{
                label: 'Assets',
                data: valueLocation,
                backgroundColor: '#fb923c', // orange tone
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false }},
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

});
</script>