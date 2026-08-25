<div wire:ignore.self>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <button wire:click="$set('isOpen', false)" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                <h3 class="font-bold text-lg mb-4">
                    {{ $vendorId ? 'Edit vendor' : 'Tambah vendor' }}
                </h3>

                <form wire:submit="save">
                    <div class="form-control mb-4">
                        <label class="label">Nama Vendor</label>
                        <input type="text" wire:model="name" class="input input-bordered w-full" />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Alamat</label>
                        <input type="text" wire:model="address" class="input input-bordered w-full" />
                        @error('address')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Nomer HP</label>
                        <input type="text" wire:model="phone" class="input input-bordered w-full" />
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-control mb-4">
                        <label class="label">Jenis Vendor</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_supplier" class="checkbox checkbox-primary">
                                <span>Supplier</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_service" class="checkbox checkbox-primary">
                                <span>Jasa Service</span>
                            </label>
                        </div>

                        @error('is_supplier')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        @error('is_service')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" class="btn btn-secondary" wire:click="$set('isOpen', false)">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                {{ $vendorId ? 'Update' : 'Simpan' }}
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
