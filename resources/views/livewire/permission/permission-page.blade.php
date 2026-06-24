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
    <input type="checkbox" id="perm-modal" class="modal-toggle" wire:model="showModal" />
    <div class="modal">
        <div class="modal-box relative">
            <label for="perm-modal" class="btn btn-sm btn-circle absolute right-2 top-2"
                wire:click="closeModal">✕</label>
            <h3 class="text-lg font-bold mb-4">{{ $modalMode === 'create' ? 'Add Permission' : 'Edit Permission' }}</h3>

            <form wire:submit.prevent="{{ $modalMode==='create' ? 'store' : 'update' }}" class="space-y-3">
                <input type="text" placeholder="Permission Name" wire:model="name"
                    class="input input-bordered w-full" />
                @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit"
                        class="btn btn-primary">{{ $modalMode==='create' ? 'Save' : 'Update' }}</button>
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>