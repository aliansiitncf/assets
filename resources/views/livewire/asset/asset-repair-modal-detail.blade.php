<div>
    @if ($isDetailOpen && $selectedRepair)
        <div class="modal modal-open">
            <div class="modal-box max-w-3xl">

                {{-- Header --}}
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-lg">Detail Perbaikan</h3>
                        <p class="text-sm text-base-content/60">
                            {{ $selectedRepair->asset->asset_code ?? '-' }} &middot;
                            {{ $selectedRepair->asset->name ?? '-' }}
                        </p>
                    </div>

                    <span
                        class="badge {{ $selectedRepair->status === 'In Progress' ? 'badge-primary' : 'badge-success' }}">
                        {{ $selectedRepair->status }}
                    </span>
                </div>

                {{-- Info utama --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div class="bg-base-200/50 rounded-lg p-3">
                        <p class="text-xs text-base-content/60 mb-1">Lokasi</p>
                        <p class="text-sm font-medium">
                            {{ $selectedRepair->asset->latestLocation->location->name ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-base-200/50 rounded-lg p-3">
                        <p class="text-xs text-base-content/60 mb-1">HM / KM</p>
                        <p class="text-sm font-medium">{{ $selectedRepair->hm_km ?? '-' }}</p>
                    </div>

                    <div class="bg-base-200/50 rounded-lg p-3">
                        <p class="text-xs text-base-content/60 mb-1">Out of Service</p>
                        <p class="text-sm font-medium">
                            {{ $selectedRepair->started_at?->format('d M Y') ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-base-200/50 rounded-lg p-3">
                        <p class="text-xs text-base-content/60 mb-1">In of Service</p>
                        <p class="text-sm font-medium">
                            {{ $selectedRepair->completed_at?->format('d M Y') ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-base-200/50 rounded-lg p-3">
                        <p class="text-xs text-base-content/60 mb-1">Poin Service</p>
                        <p class="text-sm font-medium">{{ $selectedRepair->poin ?? '-' }}</p>
                    </div>

                    <div class="bg-base-200/50 rounded-lg p-3">
                        <p class="text-xs text-base-content/60 mb-1">Dibuat</p>
                        <p class="text-sm font-medium">
                            {{ $selectedRepair->created_at?->format('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-5">
                    <p class="text-xs text-base-content/60 mb-1">Catatan Perbaikan</p>
                    <p class="text-sm bg-base-200/50 rounded-lg p-3 whitespace-pre-line">
                        {{ $selectedRepair->repair_note ?: '-' }}
                    </p>
                </div>

                {{-- Foto --}}
                @if ($selectedRepair->image_path)
                    <div class="mb-5">
                        <p class="text-xs text-base-content/60 mb-2">Foto Kerusakan</p>
                        <a href="{{ Storage::url($selectedRepair->image_path) }}" target="_blank" rel="noopener">
                            <img src="{{ Storage::url($selectedRepair->image_path) }}" alt="Foto perbaikan"
                                class="w-32 h-32 rounded-lg object-cover border hover:opacity-80 transition">
                        </a>
                    </div>
                @endif

                {{-- Daftar komponen --}}
                <div class="mb-2">
                    <p class="text-xs text-base-content/60 mb-2">
                        Komponen
                        @if ($selectedRepair->components->count())
                            <span class="badge badge-primary badge-sm ml-1">
                                {{ $selectedRepair->components->count() }}
                            </span>
                        @endif
                    </p>

                    @if ($selectedRepair->components->count())
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="table table-sm">
                                <thead class="bg-base-200">
                                    <tr>
                                        <th>Komponen</th>
                                        <th>Merk</th>
                                        <th class="text-center">Qty</th>
                                        <th>Toko</th>
                                        <th>Teknisi</th>
                                        <th>Tanggal</th>
                                        <th class="text-right">Harga</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach ($selectedRepair->components as $component)
                                        @php
                                            $subtotal = $component->pivot->qty * $component->pivot->price;
                                            $grandTotal += $subtotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $component->name_component }}</td>
                                            <td>{{ $component->pivot->merk ?: '-' }}</td>
                                            <td class="text-center">{{ $component->pivot->qty }}</td>
                                            <td>{{ $component->pivot->store ?: '-' }}</td>
                                            <td>{{ $component->pivot->technician ?: '-' }}</td>
                                            <td class="whitespace-nowrap">
                                                {{ $component->pivot->date ? \Carbon\Carbon::parse($component->pivot->date)->format('d M Y') : '-' }}
                                            </td>
                                            <td class="text-right whitespace-nowrap">
                                                Rp {{ number_format($component->pivot->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-right font-medium whitespace-nowrap">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-semibold bg-base-200">
                                        <td colspan="7" class="text-right">Total</td>
                                        <td class="text-right whitespace-nowrap">
                                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-6 text-base-content/50 border rounded-lg border-dashed text-sm">
                            Belum ada komponen tercatat.
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="modal-action">
                    <a href="{{ route('asset.repair.edit', $selectedRepair->id_asset_repair) }}" wire:navigate
                        class="btn btn-primary btn-sm">
                        Edit
                    </a>
                    <button type="button" wire:click="closeDetailModal" class="btn btn-ghost btn-sm">
                        Tutup
                    </button>
                </div>

            </div>

            {{-- Klik area luar untuk menutup --}}
            <button type="button" class="modal-backdrop" wire:click="closeDetailModal" aria-label="Tutup modal">
            </button>
        </div>
    @endif
</div>
