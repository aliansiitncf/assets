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
    <h1 class="text-2xl font-bold">Print QR Code</h1>
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <input type="text" class="input input-bordered" wire:model.live.debounce.300ms="search"
                placeholder="Search Assets..." />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        @if ($this->canAccess('cetak QR code aset'))
        <button class="btn btn-accent" wire:click="$set('showModal', true)">
            🖨 Print QR
        </button>
        @endif
    </div>
    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                                wire:model="selectAll" />
                        </label>
                    </th>
                    <th>Asset Code</th>
                    <th>Asset Name</th>
                    <th>Category</th>
                    <th>Purchase Date</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" class="checkbox checkbox-sm checkbox-primary justify-center"
                                wire:model="selected" value="{{ $asset->asset_code }}" />
                        </label>
                    </td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '-' }}</td>
                    <td>{{ $asset->purchase_date->format('d M Y') }}</td>
                    <td>{{ optional($asset->latestLocation)->location->name ?? '-' }}</td>
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
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $assets->links() }}
        </div>
    </div>
    {{-- MODAL PRINT LAYOUT --}}
    @if($showModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Pilih Layout Label</h3>
            @if ($errors->any())
            <div class="alert alert-error my-2">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <select wire:model="pageSetup" class="select select-bordered w-full">
                <option value="">-- Pilih Ukuran Label --</option>
                @foreach($pages as $page)
                <option value="{{ $page->id_page_setup }}">
                    {{ $page->size_name }} ({{ $page->width }}×{{ $page->height }} mm)
                </option>
                @endforeach
            </select>

            <div class="modal-action">
                <button class="btn btn-primary" wire:click="print">
                    Print
                </button>

                <button class="btn btn-outline" wire:click="closeModal">
                    Batal
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- END MODAL PRINT LAYOUT --}}
    <script>
        window.addEventListener('open-print-tab', (event) => {
            window.open(event.detail.url, '_blank');
        });
    </script>


</div>