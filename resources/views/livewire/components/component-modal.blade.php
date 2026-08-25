<div wire:ignore.self>
    @if($isOpen)
    <div class="modal modal-open">
        <div class="modal-box">
            <button wire:click="$set('isOpen', false)" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
            <h3 class="font-bold text-lg mb-4">
                {{ $componentId ? 'Edit Component' : 'Add Component' }}
            </h3>

            <form wire:submit="save">
                <div class="form-control mb-4">
                    <label class="label">Nama Component</label>
                    <input type="text" wire:model="name_component" placeholder="Nama Component"
                        class="input input-bordered w-full @error('name_component') input-error @enderror" autofocus>
                    @error('name_component')
                    <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-secondary" wire:click="$set('isOpen', false)">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            {{ $componentId ? 'Update' : 'Simpan' }}
                        </span>
                        <span wire:loading>Loading...</span>
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="$set('isOpen', false)"></div>
    </div>
    @endif
</div>