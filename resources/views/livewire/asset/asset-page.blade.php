<div class="w-full">
    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" role="alert" class="alert alert-success mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif
    <h1 class="text-2xl font-bold">Assets Management</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Asset Baik -->
        <div class="stats shadow bg-yellow-400 w-full mt-2">
            <div class="stat">
                <div class="stat-figure">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 h-10 text-green-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                    </svg>
                </div>
                <div class="stat-title font-semibold text-green-800">
                    Total Asset Normal
                </div>
                <div class="stat-value text-green-600">
                    {{ $assetStats['Baik'] ?? 0 }}
                </div>
                <div class="stat-desc font-semibold text-green-800">
                    Dari Total Seluruh Asset
                </div>
            </div>
        </div>

        <!-- Asset Perbaikan -->
        <div class="stats shadow bg-yellow-400 w-full mt-2">
            <div class="stat">
                <div class="stat-figure">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 h-10 text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <div class="stat-title font-semibold text-blue-800 mt-2">
                    Total Asset Perbaikan
                </div>
                <div class="stat-value text-blue-600">
                    {{ $assetStats['Perbaikan'] ?? 0 }}
                </div>
                <div class="stat-desc font-semibold text-blue-800">
                    Dari Total Seluruh Asset
                </div>
            </div>
        </div>

        <!-- Asset Rusak -->
        <div class="stats shadow bg-yellow-400 w-full mt-2">
            <div class="stat">
                <div class="stat-figure">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 h-10 text-red-800">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="stat-title font-semibold text-red-800 mt-2">
                    Total Asset Rusak
                </div>
                <div class="stat-value text-red-600">
                    {{ $assetStats['Rusak'] ?? 0 }}
                </div>
                <div class="stat-desc font-semibold text-red-800">
                    Dari Total Seluruh Asset
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col lg:flex-row lg:justify-between gap-3 my-3">
        <div class=" flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Assets..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <div class=" flex gap-2 items-center">
            @can('cetak laporan aset')
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
            <button wire:click="exportExcel" class="btn bg-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-excel">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                    <path d="M10 12l4 5" />
                    <path d="M10 17l4 -5" />
                </svg>
            </button>
            @endcan
            <button wire:click="create" class="btn btn-primary">Add Asset</button>
        </div>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>QR Code</th>
                    <th class="width=25%">Image</th>
                    <x-table-header :field="'asset_code'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Asset Code
                    </x-table-header>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Name
                    </x-table-header>
                    <th> Category</th>
                    <x-table-header :field="'purchase_date'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Purchase Date
                    </x-table-header>
                    <th>Location</th>
                    <th>Condition</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>
                        <select wire:model.live.debounce.300ms="filterCategory"
                            class="select select-xs select-primary font-normal w-32">
                            <option value="" selected>Filter</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="$set('filterCategory', null)" class="btn btn-xs btn-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </th>
                    <th></th>
                    <th>
                        <select wire:model.live.debounce.300ms="filterLocation"
                            class="select select-xs select-primary font-normal w-32">
                            <option value="" selected>Filter</option>
                            @foreach($filterLocations as $filterLocation)
                            <option value="{{ $filterLocation->id_location }}">{{ $filterLocation->name }}</option>
                            @endforeach
                        </select>
                        <button wire:click="$set('filterLocation', null)" class="btn btn-xs btn-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td class="link link-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 mx-auto"
                            wire:click="OpenModalQr({{ $asset->id_asset }})">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
                        </svg>
                    </td>
                    <td>
                        @if($asset->image_path)
                        <img src="{{ Storage::url($asset->image_path) }}?v={{ $asset->updated_at->timestamp }}"
                            alt="{{ $asset->name }}" class="w-20 h-20 object-cover rounded">
                        @else
                        <div class="w-12 h-12 flex items-center justify-center bg-gray-200 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                            </svg>
                        </div>
                        @endif
                    </td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '-' }}</td>
                    <td>{{ $asset->purchase_date->format('d M Y') }}</td>
                    {{-- <td>{{ optional($asset->latestLocation)->location->name $asset->latestLocation->details ?? '-' }}</td> --}}
                    <td>
                        <div class="badge badge-soft badge-accent">{{ optional($asset->latestLocation)->location->name ?? '-' }}</div>
                        <div class="badge badge-dash badge-secondary">{{ optional($asset->latestLocation)->details ?? '-' }}</div>
                    </td>
                    @switch($asset->condition)
                    @case('Rusak')
                    <td class="badge badge-error badge-sm ml-3 mt-8">{{ $asset->condition }}</td>
                    @break
                    @case('Perbaikan')
                    <td class="badge badge-warning badge-sm ml-3 mt-8">{{ $asset->condition }}</td>
                    @break
                    @default
                    <td class="badge badge-success badge-sm ml-3 mt-8">{{ $asset->condition }}</td>
                    @endswitch
                    <td>
                        <div class="dropdown dropdown-left dropdown-center">
                            <div tabindex="0" role="button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </div>
                            <ul tabindex="-1"
                                class="dropdown-content menu bg-base-100 gap-2 rounded-box z-1 w-40 p-2 shadow-sm">
                                <li>
                                    <button wire:click="edit({{ $asset->id_asset }})"
                                        class="btn btn-xs btn-warning "><svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                </li>
                                <li>
                                    <button wire:click="delete({{ $asset->id_asset }})"
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        class="btn btn-xs btn-error">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </li>
                                <li>
                                    <button wire:click="showDetail({{ $asset->id_asset }})" class="btn btn-xs btn-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                        </svg>
                                        <span>Detail Aset</span>
                                    </button>
                                </li>
                                <li>
                                    @switch($asset->condition)
                                    @case('Rusak')
                                    <button wire:click="openRepairModal({{ $asset->id_asset }})"
                                        class="btn btn-xs btn-secondary p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                        </svg>
                                        <span>Repair</span>
                                    </button>
                                    @break
                                    @case('Perbaikan')

                                    @break
                                    @default
                                    <button wire:click="openDamageModal({{ $asset->id_asset }})"
                                        class="btn btn-xs btn-secondary p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>
                                        <span>Mark as Damaged</span>
                                    </button>
                                    @endswitch
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500">
                        No assets found
                        @if($filterCategory)
                        in selected category.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $assets->links() }}
        </div>
    </div>
    @include('livewire.asset.asset-damage-modal')
    @include('livewire.asset.asset-repair-modal')
    @include('livewire.asset.asset-modal-qrcode')
    @include('livewire.asset.asset-modalPDF')
    @if ($showModalDetailAset && $selectedAsset)
    <livewire:asset.detail-asset :asset="$selectedAsset" wire:key="detail-asset-{{ $selectedAsset->id_asset }}" />
    @endif
</div>