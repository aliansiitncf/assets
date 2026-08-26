<div class="w-full">
    <h1 class="text-2xl font-bold">Users Management</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.800ms="search" placeholder="Search users..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <button wire:click="openModal('create')" class="btn btn-primary">Add New User</button>
    </div>
    {{-- Users Table --}}
    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5  mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <x-table-header :field="'name'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Name
                    </x-table-header>
                    <x-table-header :field="'email'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Email
                    </x-table-header>
                    <th>
                        Role
                    </th>

                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $index => $user)
                <tr wire:key="user-{{ $user->id_user }}">
                    <td>{{ $users->firstItem() + $index }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>@forelse ($roles as $role)
                        @if($user->hasRole($role->name))
                        <span class="badge badge-info mr-1">{{ $role->name }}</span>
                        @endif
                        @empty
                        <span class="badge badge-warning">-</span>
                        @endforelse
                    </td>
                    <td class="flex gap-2">
                        <button wire:click="openModal('edit', {{ $user->id_user }})"
                            class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="delete({{ $user->id_user }})"
                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                            class="btn btn-sm btn-error">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>
    {{-- Modal --}}
    @if ($showModal)
    <div class="modal modal-open">
        <div class="modal-box relative w-full max-w-md">
            <button class="btn btn-sm btn-circle absolute right-2 top-2" wire:click="closeModal">✕</button>
            <h3 class="text-lg font-bold mb-4">{{ $modalMode === 'create' ? 'Add User' : 'Edit User' }}</h3>

            <form wire:submit="{{ $modalMode === 'create' ? 'store' : 'update' }}" class="space-y-3">
                <input type="text" placeholder="Name" wire:model="name" class="input input-bordered w-full" />
                @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror

                <input type="email" placeholder="Email" wire:model="email" class="input input-bordered w-full" />
                @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror

                <select wire:model="role_id" class="select select-bordered w-full">
                    <option selected="">Select Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <span class="text-error text-sm">{{ $message }}</span> @enderror

                <input type="password" placeholder="Password" wire:model="password" class="input input-bordered w-full" />
                @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror

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