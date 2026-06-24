<div wire:ignore.self>
    @if($isOpen)
    <dialog id="component_modal" class="modal modal-open">
        <div class="modal-box">

            <h3 class="font-bold text-lg">
                {{ $componentId ? 'Edit Component' : 'Add Component' }}
            </h3>

            {{-- MESSAGE --}}
            @if(session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" role="alert" class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            @endif

            {{-- INPUT --}}
            <div class="mt-4">
                <input type="text" wire:model="name_component" placeholder="Nama Component"
                    class="input input-bordered w-full @error('name_component') input-error @enderror" autofocus>

                @error('name_component')
                <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- ACTION --}}
            <div class="modal-action">
                <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        {{ $componentId ? 'Update' : 'Simpan' }}
                    </span>
                    <span wire:loading>Loading...</span>
                </button>

                <button class="btn" wire:click="$set('isOpen', false)">
                    Batal
                </button>
            </div>
        </div>
    </dialog>
    @endif
</div>