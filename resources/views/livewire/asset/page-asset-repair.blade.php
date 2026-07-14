<div class="w-full">
    <h1 class="text-2xl font-bold">Asset Repairs</h1>

    @if (session()->has('success'))
        <div class="alert alert-success shadow-lg mt-4">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @include('livewire.asset.asset-repair-charts')

    <div class="flex flex-wrap justify-between items-end gap-3 mb-2">
        <div class="mt-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="label py-1"><span class="label-text text-xs">Lokasi Aset</span></label>
                <select wire:model.live="locationFilter" class="select select-bordered w-full">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id_location }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label py-1"><span class="label-text text-xs">Kategori Aset</span></label>
                <select wire:model.live="categoryFilter" class="select select-bordered w-full">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id_category }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="my-1 flex flex-wrap gap-3 items-end">
            <div class="min-w-[220px] flex-1">
                <label class="label py-1"><span class="label-text text-xs">Cari Asset</span></label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Assets..."
                    class="input input-bordered w-full" />
            </div>

            <div>
                <label class="label py-1"><span class="label-text text-xs">Dari tanggal</span></label>
                <input type="date" wire:model.live="startDate" class="input input-bordered w-full">
            </div>

            <div class="flex flex-col">
                <label class="label py-1"><span class="label-text text-xs invisible">-</span></label>
                <span class="text-gray-400 text-sm h-10 flex items-center justify-center px-1">&ndash;</span>
            </div>

            <div>
                <label class="label py-1"><span class="label-text text-xs">Sampai tanggal</span></label>
                <input type="date" wire:model.live="endDate" class="input input-bordered w-full">
            </div>

            <div>
                <label class="label py-1"><span class="label-text text-xs">Per halaman</span></label>
                <select wire:model.live="perPage" class="select select-bordered w-full">
                    <option value="5">5 / page</option>
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="resetFilter" class="btn btn-outline">
                    Reset Filter
                </button>

                <span wire:loading wire:target="search, startDate, endDate, resetFilter"
                    class="loading loading-spinner loading-sm"></span>
            </div>
            <button class="btn btn-secondary" wire:click="openModalPDF">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                    <path d="M17 18h2" />
                    <path d="M20 15h-3v6" />
                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1" />
                </svg>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-xs table-zebra w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Asset Code</th>
                    <th>Asset Name</th>
                    <th>Notes</th>
                    <th>Image</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Out Service</th>
                    <th>In Service</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assetRepairs as $assetRepair)
                    <tr>
                        <td>{{ $loop->iteration + ($assetRepairs->currentPage() - 1) * $assetRepairs->perPage() }}</td>
                        <td>{{ $assetRepair->asset->asset_code }}</td>
                        <td>{{ $assetRepair->asset->name }}</td>
                        <td>{{ $assetRepair->repair_note }}</td>
                        <td>
                            @if ($assetRepair->image_path)
                                <img src="{{ Storage::url($assetRepair->image_path) }}" alt="Repair Image"
                                    class="w-16 h-16 object-cover rounded">
                            @else
                                <span class="text-gray-400 text-xs">empty image</span>
                            @endif
                        </td>
                        <td>{{ $assetRepair->asset->latestLocation->location->name ?? '-' }}</td>
                        <td>
                            <span
                                class="badge badge-xs {{ $assetRepair->status === 'In Progress' ? 'badge-primary' : 'badge-success' }}">
                                {{ $assetRepair->status }}
                            </span>
                        </td>
                        <td>{{ $assetRepair->started_at ? \Carbon\Carbon::parse($assetRepair->started_at)->format('d M Y') : '-' }}
                        </td>
                        <td>{{ $assetRepair->completed_at ? \Carbon\Carbon::parse($assetRepair->completed_at)->format('d M Y') : '-' }}
                        </td>
                        <td class="flex gap-2">
                            <a href="{{ route('asset.repair.edit', $assetRepair->id_asset_repair) }}" wire:navigate
                                class="btn btn-success btn-xs">
                                Edit
                            </a>
                            <button type="button" wire:click="showDetail({{ $assetRepair->id_asset_repair }})"
                                class="btn btn-ghost btn-xs">
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-gray-500">
                            No asset Repair found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $assetRepairs->links() }}
        </div>
    </div>
    @include('livewire.asset.asset-repair-modalPDF')
    @include('livewire.asset.asset-repair-modal-detail')
</div>
