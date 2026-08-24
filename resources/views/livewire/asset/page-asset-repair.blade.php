<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-light text-gray-800 tracking-tight">Riwayat Perbaikan Aset</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola dan pantau riwayat perbaikan aset</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
            {{ $assets->total() }} aset
        </span>
    </div>

    {{-- Charts --}}
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4">
        @include('livewire.asset.asset-repair-charts')
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4 space-y-4 mt-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Lokasi</label>
                <select wire:model.live="locationFilter"
                    class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id_location }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                <select wire:model.live="categoryFilter"
                    class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Cari aset</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Kode atau nama aset..."
                        class="w-full pl-9 pr-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Dari</label>
                <input type="date" wire:model.live="startDate"
                    class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Sampai</label>
                <input type="date" wire:model.live="endDate"
                    class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Tampilkan</label>
                <select wire:model.live="perPage"
                    class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-50 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="5">5 data</option>
                    <option value="10">10 data</option>
                    <option value="25">25 data</option>
                    <option value="50">50 data</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="button" wire:click="resetFilter"
                    class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-200 rounded-md text-sm font-medium text-gray-600 hover:bg-warning hover:border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors">
                    <span class="flex items-center flex-row"><x-heroicon-c-arrow-path class="w-5 h-5 mr-2" />Reset
                        Filter</span>
                </button>
                <div wire:loading wire:target="search, startDate, endDate, resetFilter"
                    class="inline-block w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin">
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-gray-400">
                <i class="ti ti-info-circle mr-1"></i> Filter aktif akan mempengaruhi data yang ditampilkan
            </span>
            <button
                class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 rounded-md text-sm font-medium hover:bg-red-100 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors"
                wire:click="openModalPDF">
                <span class="flex flex-row items-center"><x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />Unduh
                    Data Repair (PDF)</span>
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden mt-4">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-100">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Kode aset</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Nama aset</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Lokasi</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Perbaikan</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() }}
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-800">{{ $asset->asset_code }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $asset->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ optional($asset->latestLocation)->location->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-sm">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ $asset->repairs_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-row items-center gap-3">
                                    <button type="button" wire:click="toggleAsset({{ $asset->id_asset }})"
                                        class="inline-flex items-center text-sm font-medium {{ $selectedAssetId === $asset->id_asset ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                                        <i class="ti ti-tools mr-1.5 text-base"></i>
                                        {{ $selectedAssetId === $asset->id_asset ? 'Tutup' : 'Lihat riwayat' }}
                                    </button>
                                    <button type="button" wire:click="exportRepairExcel({{ $asset->id_asset }})"
                                        class="btn btn-success btn-sm">
                                        Unduh Riwayat
                                    </button>
                                </div>
                            </td>
                        </tr>

                        @if ($selectedAssetId === $asset->id_asset)
                            <tr>
                                <td colspan="6" class="px-4 py-4 bg-gray-50/30">
                                    <div class="space-y-3">
                                        <div
                                            class="flex items-center gap-2 text-xs font-medium text-gray-400 uppercase tracking-wider">
                                            <i class="ti ti-list-details"></i>
                                            <span>Riwayat perbaikan </span> <span
                                                class="font-semibold text-gray-700">{{ $asset->asset_code }} -
                                                {{ $asset->name }}</span>
                                            <div class="flex-1 h-px bg-gray-200"></div>
                                            <span
                                                class="ml-auto bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs">
                                                {{ $assetRepairsForSelected->count() }}
                                            </span>
                                        </div>

                                        @forelse ($assetRepairsForSelected as $assetRepair)
                                            <div
                                                class="bg-white rounded border border-gray-100 p-4 hover:shadow-sm transition-shadow">

                                                <div
                                                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_2fr_auto] gap-5 text-sm">

                                                    {{-- Kolom 1: Out / In Service --}}
                                                    <div class="space-y-3">
                                                        <div>
                                                            <span
                                                                class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                                                                Out of Service
                                                            </span>

                                                            <span class="text-gray-700">
                                                                {{ $assetRepair->started_at ? \Carbon\Carbon::parse($assetRepair->started_at)->translatedFormat('d F Y') : '-' }}
                                                            </span>
                                                        </div>

                                                        <div>
                                                            <span
                                                                class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                                                                In of Service
                                                            </span>

                                                            <span class="text-gray-700">
                                                                {{ $assetRepair->completed_at
                                                                    ? \Carbon\Carbon::parse($assetRepair->completed_at)->translatedFormat('d F Y')
                                                                    : '-' }}
                                                            </span>
                                                        </div>
                                                    </div>


                                                    {{-- Kolom 2: Status & Total --}}
                                                    <div class="space-y-3">
                                                        <div>
                                                            <span
                                                                class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                                                                Status
                                                            </span>

                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $assetRepair->status === 'In Progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                                                {{ $assetRepair->status === 'In Progress' ? 'Sedang diperbaiki' : 'Selesai' }}
                                                            </span>
                                                        </div>

                                                        {{-- Hitung Total --}}
                                                        @php
                                                            $grandTotal = 0;

                                                            foreach ($assetRepair->components as $component) {
                                                                $subtotal =
                                                                    $component->pivot->subtotal ??
                                                                    ($component->pivot->qty ?? 0) *
                                                                        ($component->pivot->price ?? 0);

                                                                $grandTotal += $subtotal;
                                                            }
                                                        @endphp

                                                        <div>
                                                            <span
                                                                class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                                                                Total Biaya
                                                            </span>

                                                            <span class="font-semibold text-gray-800">
                                                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                    </div>


                                                    {{-- Kolom 3: Catatan Perbaikan --}}
                                                    <div class="min-w-0">
                                                        <span
                                                            class="block text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">
                                                            Catatan Perbaikan
                                                        </span>

                                                        <p class="text-gray-600 leading-relaxed break-words">
                                                            {{ $assetRepair->repair_note ?: '-' }}
                                                        </p>
                                                    </div>


                                                    {{-- Kolom 4: Action --}}
                                                    <div
                                                        class="flex flex-row lg:flex-col items-start lg:items-center justify-start gap-2">

                                                        <a href="{{ route('asset.repair.edit', $assetRepair->id_asset_repair) }}"
                                                            wire:navigate
                                                            class="inline-flex items-center justify-center px-3 py-1.5
                           bg-green-50 text-green-600 rounded text-xs font-medium
                           hover:bg-green-100 transition-colors whitespace-nowrap">
                                                            <i class="ti ti-edit mr-1 text-sm"></i>
                                                            Edit
                                                        </a>

                                                        <button type="button"
                                                            wire:click="showDetail({{ $assetRepair->id_asset_repair }})"
                                                            class="inline-flex items-center justify-center px-3 py-1.5
                           bg-gray-50 text-gray-600 rounded text-xs font-medium
                           hover:bg-gray-100 transition-colors whitespace-nowrap">
                                                            <i class="ti ti-eye mr-1 text-sm"></i>
                                                            Detail
                                                        </button>

                                                    </div>

                                                </div>
                                            </div>

                                        @empty

                                            <div class="text-center py-6 text-sm text-gray-400">
                                                <i class="ti ti-inbox text-2xl block mb-1.5"></i>
                                                <span>Belum ada riwayat perbaikan</span>
                                            </div>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-400">
                                <i class="ti ti-database-off text-3xl block mb-2"></i>
                                <span>Belum ada data aset</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div
            class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="text-xs text-gray-400">
                Menampilkan {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} dari
                {{ $assets->total() }}
            </span>
            <div>
                {{ $assets->links() }}
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @include('livewire.asset.asset-repair-modalPDF')
    @include('livewire.asset.asset-repair-modal-detail')
</div>
