<div class="w-full">
    <h1 class="text-2xl font-bold">Teknisi Management</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Technicians..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <button wire:click="openModal('create')" class="btn btn-primary">Tambah Teknisi</button>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Nama
                    </x-table-header>
                    <x-table-header :field="'phone'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Nomer HP
                    </x-table-header>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($technicians as $technician)
                    <tr>
                        <td>{{ $technician->name }}</td>
                        <td>{{ $technician->phone }}</td>
                        <td class="text-right space-x-2">
                            <button wire:click="openModal('edit',{{ $technician->id }})"
                                class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $technician->id }})" onclick="confirm('Are you sure?')"
                                class="btn btn-sm btn-error">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4">Tidak ada data teknisi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $technicians->links() }}
        </div>

    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <button wire:click="closeModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                <h3 class="font-bold text-lg mb-4">{{ $modalMode === 'create' ? 'Tambah Teknisi' : 'Edit Teknisi' }}</h3>
                <form wire:submit="store">
                    <div class="form-control mb-4">
                        <label class="label">Nama Teknisi</label>
                        <input type="text" wire:model="name" class="input input-bordered w-full" />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Nomer HP</label>
                        <input type="text" wire:model="phone" class="input input-bordered w-full" />
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">{{ $modalMode === 'create' ? 'Create' : 'Update' }}</button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
