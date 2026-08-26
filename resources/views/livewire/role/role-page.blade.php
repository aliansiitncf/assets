<div class="w-full">
    <h1 class="text-2xl mb-2 font-bold">Role Management</h1>
    <div class="flex justify-between mb-4">
        <input type="text" wire:model.live.debounce.800ms="search" placeholder="Search roles..."
            class="input input-bordered w-1/2" />
        <button wire:click="openModal('create')" class="btn btn-primary">Add New Role</button>
    </div>

    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">Role Name
                    </x-table-header>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $index => $role)
                    <tr wire:key="role-{{ $role->id }}">
                        <td>{{ $roles->firstItem() + $index }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @forelse ($role->permissions as $permission)
                                    <span class="badge badge-outline badge-sm">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-400">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="flex gap-2">
                            <button wire:click="openModal('edit', {{ $role->id }})"
                                class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $role->id }})" class="btn btn-sm btn-error"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 p-3">
            {{ $roles->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if ($showModal)
    <div class="modal modal-open">
        <div class="modal-box w-11/12 max-w-5xl">
            <button class="btn btn-sm btn-circle absolute right-2 top-2" wire:click="closeModal">✕</button>
            <h3 class="text-lg font-bold mb-4">{{ $modalMode === 'create' ? 'Add Role' : 'Edit Role' }}</h3>

            <form wire:submit="{{ $modalMode === 'create' ? 'store' : 'update' }}" class="space-y-3">
                <input type="text" placeholder="Role Name" wire:model="name" class="input input-bordered w-full" />
                @error('name')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror

                <label class="text-xl font-semibold text-red-600">Permissions</label>
                @foreach ($this->groupedPermissions() as $group => $permissions)
                    <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                        <input type="radio" name="my-accordion-2" checked="checked" />
                        <div class="collapse-title font-semibold">{{ ucfirst($group) }}</div>
                        <div class="collapse-content text-sm">
                            <div class="grid grid-cols-2 gap-2 max-h-40">
                                @foreach ($permissions as $perm)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" class="toggle toggle-warning" wire:model="permissions"
                                            value="{{ $perm->id }}" />
                                        {{ $perm->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                @error('permissions')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">{{ $modalMode === 'create' ? 'Save' : 'Update' }}</button>
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="closeModal"></div>
    </div>
    @endif
</div>
