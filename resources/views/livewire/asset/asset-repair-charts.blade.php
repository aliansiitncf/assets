<div class="mb-6" x-data="{
    poinChart: null,
    biayaChart: null,
    initCharts() {
        const poinCtx = document.getElementById('chart-poin-asset');
        const biayaCtx = document.getElementById('chart-biaya-asset');
        if (!poinCtx || !biayaCtx) return;

        if (this.poinChart) {
            this.poinChart.destroy();
            this.poinChart = null;
        }
        if (this.biayaChart) {
            this.biayaChart.destroy();
            this.biayaChart = null;
        }

        const poinData = JSON.parse(document.getElementById('chart-poin-data').textContent);
        const biayaData = JSON.parse(document.getElementById('chart-biaya-data').textContent);

        const customBgPlugin = {
            id: 'customCanvasBackgroundColor',
            beforeDraw: (chart, args, options) => {
                const { ctx } = chart;
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = options.color || '#ffffff';
                ctx.fillRect(0, 0, chart.width, chart.height);
                ctx.restore();
            }
        };

        this.poinChart = new Chart(poinCtx, {
            type: 'bar',
            data: {
                labels: poinData.labels,
                datasets: [{
                    label: 'Poin Service',
                    data: poinData.values,
                    backgroundColor: '#378ADD',
                    borderRadius: 4,
                    maxBarThickness: 36,
                }],
            },
            plugins: [customBgPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });

        this.biayaChart = new Chart(biayaCtx, {
            type: 'bar',
            data: {
                labels: biayaData.labels,
                datasets: [{
                    label: 'Biaya Perbaikan',
                    data: biayaData.values,
                    backgroundColor: '#D85A30',
                    borderRadius: 4,
                    maxBarThickness: 36,
                }],
            },
            plugins: [customBgPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + value.toLocaleString('id-ID'),
                        },
                    },
                },
            },
        });
    }
}" x-on:charts-updated.window="$nextTick(() => initCharts())"
    x-on:livewire:navigated.window="$nextTick(() => initCharts())">
    {{-- Data JSON untuk chart. TIDAK pakai wire:ignore supaya selalu ikut ter-update oleh Livewire --}}
    <script type="application/json" id="chart-poin-data">@json($this->poinChartData)</script>
    <script type="application/json" id="chart-biaya-data">@json($this->biayaChartData)</script>

    {{-- Chart --}}
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-base font-semibold">Grafik Perbaikan</h2>
        <div class="flex gap-2">
            <button type="button"
                class="btn btn-sm btn-outline text-red-600 border-red-200 hover:bg-red-50 hover:border-red-300"
                @click="$wire.exportCharts('pdf', poinChart?.toBase64Image(), biayaChart?.toBase64Image())">
                <i class="ti ti-file-type-pdf mr-1 text-base"></i> Graph (pdf)
            </button>
            <button type="button"
                class="btn btn-sm btn-outline text-green-600 border-green-200 hover:bg-green-50 hover:border-green-300"
                @click="$wire.exportCharts('excel', poinChart?.toBase64Image(), biayaChart?.toBase64Image())">
                <i class="ti ti-file-spreadsheet mr-1 text-base"></i> Graph (excel)
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card bg-base-100 shadow-md border border-base-content/5 p-4">
            <p class="font-semibold text-sm mb-3">Poin Service per Asset</p>
            <div class="relative h-64" wire:ignore>
                <canvas id="chart-poin-asset"></canvas>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border border-base-content/5 p-4">
            <p class="font-semibold text-sm mb-3">Biaya Perbaikan per Asset</p>
            <div class="relative h-64" wire:ignore>
                <canvas id="chart-biaya-asset"></canvas>
            </div>
        </div>
    </div>
</div>
