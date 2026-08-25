<div class="w-full">
    <h1 class="text-2xl font-bold">Component Management</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Component..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <button class="btn btn-primary" wire:click="$dispatch('openComponentModal')">
            Add Component
        </button>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <x-table-header :field="'name_component'" :sortField="$sortField" :sortDirection="$sortDirection">
                        Nama Komponen
                    </x-table-header>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($components as $component)
                <tr>
                    <td>{{ $component->name_component }}</td>
                    <td>
                        <button wire:click="$dispatch('editComponentModal', {id: {{ $component->id_component }}})"
                            class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="delete({{ $component->id_component }})" onclick="confirm('Are you sure?')"
                            class="btn btn-sm btn-error">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500">
                        No component found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 p-3">
            {{ $components->links() }}
        </div>

    </div>
@livewire('components.component-modal')
</div>