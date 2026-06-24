<div class="w-full">
    <h1 class="text-2xl font-bold">Asset Damaged</h1>
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
                    <th>Reported At</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assetDamages as $assetDamage)
                <tr>
                    <td>{{ $loop->iteration + ($assetDamages->currentPage() - 1) * $assetDamages->perPage() }}</td>
                    <td>{{ $assetDamage->asset->asset_code }}</td>
                    <td>{{ $assetDamage->asset->name }}</td>
                    <td>{{ $assetDamage->damage_note }}</td>
                    <td>
                        @if ($assetDamage->image_path)
                        <img src="{{ Storage::url($assetDamage->image_path) }}" alt="Damage Image"
                            class="w-16 h-16 object-cover rounded">
                        @else
                        empty image
                        @endif
                    </td>
                    <td>{{ $assetDamage->reported_at }}</td>
                    <td>{{ $assetDamage->asset->latestLocation->location->name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-gray-500">
                        No asset Damaged found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $assetDamages->links() }}
        </div>
    </div>
    @include('livewire.asset.asset-damage-modalPDF')
</div>