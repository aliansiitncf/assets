<input type="checkbox" class="modal-toggle" wire:model="showQrModal" />
<div class="modal">
    <div class="modal-box relative">

        <button wire:click="closeQrModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
        <h3 class="font-bold text-lg">{{ $name }}</h3>
        <p class="text-sm text-gray-500">{{ $asset_code }}</p>
        <div class="flex justify-center mt-4">
            <img src="data:image/svg+xml;base64,{{ $assetQr }}" alt="QR Code">
        </div>
    </div>
</div>