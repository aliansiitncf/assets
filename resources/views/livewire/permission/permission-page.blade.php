<div class="w-full">
    <h1 class="text-2xl mb-2 font-bold">Permission Management</h1>
    <div class="flex justify-between mb-4">
        <input type="text" wire:model.live.debounce.800ms="search" placeholder="Search permissions..."
            class="input input-bordered w-1/2" />
        <button wire:click="openModal('create')" class="btn btn-primary">Add New Permission</button>
    </div>

    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 ">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $index => $perm)
                <tr wire:key="perm-{{ $perm->id }}">
                    <td>{{ $permissions->firstItem() + $index }}</td>
                    <td>{{ $perm->name }}</td>
                    <td class="flex gap-2">
                        <button wire:click="openModal('edit', {{ $perm->id }})"
                            class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="delete({{ $perm->id }})" class="btn btn-sm btn-error"
                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 p-3">
            {{ $permissions->links()}}
        </div>
    </div>

    {{-- Modal --}}
    @if ($showModal)
    <div class="modal modal-open">
        <div class="modal-box relative">
            <button class="btn btn-sm btn-circle absolute right-2 top-2" wire:click="closeModal">✕</button>
            <h3 class="text-lg font-bold mb-4">{{ $modalMode === 'create' ? 'Add Permission' : 'Edit Permission' }}</h3>

            <form wire:submit="{{ $modalMode==='create' ? 'store' : 'update' }}" class="space-y-3">
                <input type="text" placeholder="Permission Name" wire:model="name"
                    class="input input-bordered w-full" />
                @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">{{ $modalMode==='create' ? 'Save' : 'Update' }}</button>
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="closeModal"></div>
    </div>
    @endif
</div>