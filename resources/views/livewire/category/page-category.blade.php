<div class="w-full">
    <h1 class="text-2xl font-bold">Category Management</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Categories..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <button wire:click="openModal('create')" class="btn btn-primary">Add Category</button>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Name
                    </x-table-header>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-right space-x-2">
                            <button wire:click="openModal('edit',{{ $category->id_category }})"
                                class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $category->id_category }})" onclick="confirm('Are you sure?')"
                                class="btn btn-sm btn-error">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $categories->links() }}
        </div>

    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <button wire:click="closeModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                <h3 class="font-bold text-lg mb-4">{{ $modalMode === 'create' ? 'Add New Category' : 'Edit Category' }}</h3>
                <form wire:submit="store">
                    <div class="form-control mb-4">
                        <label class="label">Name</label>
                        <input type="text" wire:model="name" class="input input-bordered w-full" />
                        @error('name')
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
