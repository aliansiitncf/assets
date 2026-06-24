<div class="p-4 w-full">
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
    <h1 class="text-2xl font-bold">Page Setup</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex gap-2 items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Pages..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <button wire:click="$set('showModal', true)" class="btn btn-primary">Add Size</button>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Size Name</th>
                    <th>Column</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th>Gap Horizontal</th>
                    <th>Gap Vertical</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $page)
                <tr>
                    <td>{{ $page->size_name }}</td>
                    <td>{{ $page->column }}</td>
                    <td>{{ $page->width }} mm</td>
                    <td>{{ $page->height }} mm</td>
                    <td>{{ $page->gap_horizontal }} mm</td>
                    <td>{{ $page->gap_vertical }} mm</td>
                    <td>
                        <button wire:click="openModal('edit',{{ $page->id_page_setup }})"
                            class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="delete({{ $page->id_page_setup }})" onclick="confirm('Are you sure?')"
                            class="btn btn-sm btn-error">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    {{-- Modal --}}
    <input type="checkbox" id="size-modal" class="modal-toggle" wire:model="showModal" />
    <div class="modal">
        <div class="modal-box relative">
            <label for="size-modal" class="btn btn-sm btn-circle absolute right-2 top-2"
                wire:click="closeModal">✕</label>
            <h3 class="text-lg font-bold mb-4">{{ $modalMode === 'create' ? 'Add Size' : 'Edit Size' }}</h3>
            @if ($errors->any())
            <div class="alert alert-error mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form wire:submit.prevent="save" method="POST"
                class="space-y-3">
                @csrf
                <input type="text" placeholder="Size Name" wire:model="size_name" class="input input-bordered w-full" />
                <input type="number" placeholder="Column" wire:model="column" class="input input-bordered w-full"/>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <input type="decimal" placeholder="Width" wire:model="width" class="input input-bordered w-full"/>
                    <input type="decimal" placeholder="Height" wire:model="height" class="input input-bordered w-full" />               
                    <input type="decimal" placeholder="Gap Horizontal" wire:model="gap_horizontal"
                        class="input input-bordered w-full" />
                    <input type="decimal" placeholder="Gap Vertical" wire:model="gap_vertical"
                        class="input input-bordered w-full" />
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit"
                        class="btn btn-primary">{{ $modalMode === 'create' ? 'Save' : 'Update' }}</button>
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>