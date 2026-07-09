<div class="w-full">
    <h1 class="text-2xl font-bold">Asset Repairs</h1>
    <div class="flex justify-between items-center mb-2">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Assets..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
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
    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
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
                                class="btn btn-success btn-sm">
                                Edit
                            </a>
                            <button type="button" wire:click="showDetail({{ $assetRepair->id_asset_repair }})"
                                class="btn btn-ghost btn-sm">
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
