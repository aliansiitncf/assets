@if ($showDamageModal)
<div class="modal modal-open">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Report Damage</h3>
        <form wire:submit.prevent="saveDamage">
            <div class="py-4">
                <label for="damageNotes" class="block mb-2">Notes:</label>
                <textarea id="damageNotes" wire:model.live="damageNotes"
                    class="textarea textarea-bordered w-full"></textarea>
                @error('damageNotes') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="py-4">
                <label for="damageImage" class="block mb-2">Image:</label>
                <div class="flex justify-start items-center space-x-3">
                    <div>
                        <input type="file" id="damageImage" wire:model.live="damageImage" accept="image/*"
                            class="file-input file-input-bordered w-full max-w-xs" />
                        <label class="label">Max size 2MB</label>
                    </div>
                    {{-- Spinner saat loading --}}
                    <span wire:loading wire:target="damageImage"
                        class="loading loading-spinner loading-md ml-2 mt-2"></span>
                    {{-- preview image --}}
                    @if ($damageImage)
                    <div class="mt-2">
                        <img src="{{ $damageImage->temporaryUrl() }}" alt="Image Preview"
                            class="w-32 h-32 object-cover rounded">
                    </div>
                    <button type="button" class="btn btn-sm mt-2 btn-error" wire:click="resetImageDamage">Remove</button>
                    @endif
                </div>
                {{-- error --}}
                @error('damageImage') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="modal-action">
                <button type="button" wire:click="closeDamageModal" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <span wire:loading.remove>Save</span>
                    <span wire:loading wire:target="saveDamage" class="loading loading-spinner loading-md"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif