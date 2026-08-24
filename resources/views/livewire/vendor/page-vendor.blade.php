<div class="w-full">
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" role="alert" class="alert alert-success mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    <h1 class="text-2xl font-bold">Vendor Management</h1>
    <div class="flex justify-between items-center mb-4 w-full">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Categories..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-48">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>

            <select wire:model.live="filterJenis" class="select select-bordered w-48">
                <option value="">Semua Jenis</option>
                <option value="supplier">Supplier</option>
                <option value="service">Teknisi</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button wire:click="openExportModal" class="btn btn-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download Excel
            </button>
            <button wire:click="openModal('create')" class="btn btn-primary">Tambah vendor</button>
        </div>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Nama
                    </x-table-header>
                    <x-table-header :field="'address'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Alamat
                    </x-table-header>
                    <x-table-header :field="'phone'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Nomer HP
                    </x-table-header>
                    <x-table-header :field="'jenis'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Jenis
                    </x-table-header>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->name }}</td>
                        <td>{{ $vendor->address }}</td>
                        <td>{{ $vendor->phone }}</td>
                        <td>
                            @if ($vendor->is_supplier && $vendor->is_service)
                                Supplier & Teknisi
                            @elseif ($vendor->is_supplier)
                                Supplier
                            @elseif ($vendor->is_service)
                                Teknisi
                            @else
                                Tidak Diketahui
                            @endif
                        </td>
                        <td class="text-right space-x-2">
                            <button wire:click="openModal('edit',{{ $vendor->id }})"
                                class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $vendor->id }})" onclick="confirm('Are you sure?')"
                                class="btn btn-sm btn-error">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data vendor.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $vendors->links('vendor.pagination.custom') }}
        </div>

    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $modalMode === 'create' ? 'Tambah Vendor' : 'Edit Vendor' }}
                </h3>
                <form wire:submit.prevent="store">
                    <div class="form-control mb-4">
                        <label class="label">Nama Vendor</label>
                        <input type="text" wire:model.defer="name" class="input input-bordered w-full" />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Alamat</label>
                        <input type="text" wire:model.defer="address" class="input input-bordered w-full" />
                        @error('address')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Nomer HP</label>
                        <input type="text" wire:model.defer="phone" class="input input-bordered w-full" />
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Jenis Vendor</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.defer="is_supplier" class="checkbox checkbox-primary">
                                <span>Supplier</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.defer="is_service" class="checkbox checkbox-primary">
                                <span>Jasa Service</span>
                            </label>
                        </div>

                        @error('is_supplier')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        @error('is_service')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit"
                            class="btn btn-primary">{{ $modalMode === 'create' ? 'Create' : 'Update' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Export Excel --}}
    @if ($showExportModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Download Data Vendor</h3>
                <form wire:submit.prevent="exportVendor">
                    <div class="form-control mb-4">
                        <label class="label">Tanggal Mulai <span class="text-xs text-gray-400">(opsional)</span></label>
                        <input type="date" wire:model.defer="exportStartDate" class="input input-bordered w-full" />
                        @error('exportStartDate')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Tanggal Akhir <span class="text-xs text-gray-400">(opsional)</span></label>
                        <input type="date" wire:model.defer="exportEndDate" class="input input-bordered w-full" />
                        @error('exportEndDate')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Jenis Vendor</label>
                        <select wire:model.defer="exportFilterJenis" class="select select-bordered w-full">
                            <option value="">Semua Jenis</option>
                            <option value="supplier">Supplier</option>
                            <option value="service">Teknisi</option>
                        </select>
                    </div>
                    <div class="modal-action">
                        <button type="button" wire:click="closeExportModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
