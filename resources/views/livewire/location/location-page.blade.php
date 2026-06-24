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
    <h1 class="text-2xl font-bold">Locations Management</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Location..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <button wire:click="openModal('create')" class="btn btn-primary">Add Location</button>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Name
                    </x-table-header>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                <tr>
                    <td>{{ $location->name }}</td>
                    <td>
                        <button wire:click="openModal('edit',{{ $location->id_location}})"
                            class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="delete({{ $location->id_location }})" onclick="confirm('Are you sure?')"
                            class="btn btn-sm btn-error">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center">No locations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">
            {{ $locations->links() }}
        </div>

    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ $modalMode === 'create' ? 'Add New location' : 'Edit location' }}</h3>
            <form wire:submit.prevent="store">
                <div class="form-control mb-4">
                    <label class="label">Name</label>
                    <input type="text" wire:model.defer="name" class="input input-bordered w-full" autofocus />
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="modal-action">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="btn btn-secondary">Cancel</button>
                    <button type="submit"
                        class="btn btn-primary">{{ $modalMode === 'create' ? 'Create' : 'Update' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>