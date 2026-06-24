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
    <input type="checkbox" id="user-modal" class="modal-toggle" wire:model="showModal" />
    <div class="modal">
        <div class="modal-box relative w-full max-w-md">
            <label for="user-modal" class="btn btn-sm btn-circle absolute right-2 top-2"
                wire:click="closeModal">✕</label>
            <h3 class="text-lg font-bold mb-4">{{ $modalMode === 'create' ? 'Add User' : 'Edit User' }}</h3>

            <form wire:submit.prevent="{{ $modalMode === 'create' ? 'store' : 'update' }}" method="POST" class="space-y-3">
                @csrf
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

                <input type="password" placeholder="Password" wire:model="password"
                    class="input input-bordered w-full" />
                @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror

                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit"
                        class="btn btn-primary">{{ $modalMode === 'create' ? 'Save' : 'Update' }}</button>
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>