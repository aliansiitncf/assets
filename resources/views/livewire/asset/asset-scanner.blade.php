<div>
    <!-- Button -->
    <button class="btn btn-primary" onclick="scanner_modal.showModal()" wire:click="$dispatch('open-scanner')">
        📷 Scan Asset
    </button>

    <!-- MODAL SCANNER -->
    <dialog id="scanner_modal" class="modal" wire:ignore.self>
        <div class="modal-box w-full max-w-md">
            <h3 class="font-bold text-lg mb-3">Scan QR Code Asset</h3>

            <!-- RESULT -->
            <div class="mb-3 p-3 border rounded bg-base-200">
                @if($asset)
                <p><b>Kode:</b> {{ $asset->asset_code }}</p>
                <p><b>Nama:</b> {{ $asset->name }}</p>
                <p><b>Lokasi:</b> {{ optional($asset->latestLocation->location)->name ?? 'Tidak ada lokasi' }}</p>
                <p><b>Status:</b> {{ $asset->condition }}</p>
                @if($asset->image_path)
                <img src="{{ Storage::url($asset->image_path) }}" alt="{{ $asset->name }}"
                    class="w-24 h-24 object-cover rounded">
                @else
                <div class="w-12 h-12 flex items-center justify-center bg-gray-200 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                    </svg>
                </div>
                @endif
                @else
                <p class="text-gray-500">Silakan scan QR asset…</p>
                @endif
            </div>

            <!-- SCANNER -->
            <div wire:ignore>
                <div id="qr-reader" class="w-full"></div>
            </div>

            <div class="modal-action">
                <button class="btn" onclick="closeScannerModal()">
                    Tutup
                </button>
            </div>
        </div>
    </dialog>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            let html5QrCode = null;
            let scanned = false;

            Livewire.on('open-scanner', () => {
                scanned = false;

                html5QrCode = new Html5Qrcode("qr-reader");

                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 250 },
                    (decodedText) => {
                        if (scanned) return;

                        scanned = true;

                        // kirim STRING SAJA
                        Livewire.dispatch('assetScanned', { assetCode: decodedText });

                        // allow scan ulang setelah 2 detik
                        setTimeout(() => {
                            scanned = false;
                        }, 2000);
                    }
                );
            });

            window.closeScannerModal = function () {
                // tutup modal
                scanner_modal.close();

                // stop kamera
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        html5QrCode = null;
                    });
                }

                // reset data Livewire
                Livewire.dispatch('reset-scanner');
            };

            Livewire.on('scan-failed', () => {
                alert('QR Code tidak ditemukan');
            });
        });
    </script>
</div>