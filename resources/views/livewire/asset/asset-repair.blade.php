<div>
    {{-- Error alert --}}
    @if ($errors->any())
        <div role="alert" class="alert alert-error alert-soft mt-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow-xl p-6">
        <form wire:submit.prevent="store">
            <div class="tabs tabs-lift shadow-md rounded-2xl mt-3">

                {{-- Tab 1: Repair Info --}}
                <label class="tab">
                    <input type="radio" name="my_tabs_4" checked="checked" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                    <span class="text-sm font-medium">Info Perbaikan</span>
                </label>

                <div class="tab-content bg-base-100 border-base-300 p-6">
                    <fieldset class="fieldset">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- HM/KM --}}
                            <div>
                                <label for="hmkm" class="label">
                                    <span class="label-text font-semibold">HM/KM</span>
                                </label>
                                <input type="text" id="hmkm" wire:model.live="hmkm" placeholder="Masukkan HM/KM"
                                    class="input input-bordered input-sm w-full @error('hmkm') input-error @enderror">

                                @error('hmkm')
                                    <span class="text-sm text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Poin Service --}}
                            <div>
                                <label for="poin" class="label">
                                    <span class="label-text font-semibold">Poin Service</span>
                                </label>
                                <input type="text" id="poin" wire:model.live="poin"
                                    class="input input-bordered input-sm w-full @error('poin') input-error @enderror">

                                @error('poin')
                                    <span class="text-sm text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Out of Service --}}
                            <div>
                                <label for="out_of_service" class="label">
                                    <span class="label-text font-semibold">Out of Service</span>
                                </label>
                                <input type="date" id="out_of_service" wire:model.live="out_of_service"
                                    class="input input-bordered input-sm w-full @error('out_of_service') input-error @enderror">

                                @error('out_of_service')
                                    <span class="text-sm text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- In of Service --}}
                            <div>
                                <label for="in_of_service" class="label">
                                    <span class="label-text font-semibold">In of Service</span>
                                </label>
                                <input type="date" id="in_of_service" wire:model.live="in_of_service"
                                    class="input input-bordered input-sm w-full @error('in_of_service') input-error @enderror">

                                @error('in_of_service')
                                    <span class="text-sm text-error">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        {{-- Catatan --}}
                        <div class="mt-5">
                            <label for="repairNotes" class="label">
                                <span class="label-text font-semibold">Catatan Perbaikan</span>
                            </label>

                            <textarea id="repairNotes" rows="4" wire:model.live="repairNotes"
                                placeholder="Tuliskan kondisi kerusakan, tindakan, atau catatan lainnya..."
                                class="textarea textarea-bordered w-full @error('repairNotes') textarea-error @enderror"></textarea>

                            @error('repairNotes')
                                <span class="text-sm text-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Foto --}}
                        <div class="mt-5">
                            <label for="repairImage" class="label">
                                <span class="label-text font-semibold">Foto Kerusakan</span>
                            </label>

                            <div class="flex flex-wrap items-start gap-4">

                                <div class="flex items-center gap-2">
                                    <input type="file" id="repairImage" accept="image/*"
                                        wire:model.live="repairImage"
                                        class="file-input file-input-bordered file-input-sm w-full max-w-sm @error('repairImage') file-input-error @enderror">

                                    <span wire:loading wire:target="repairImage"
                                        class="loading loading-spinner loading-sm"></span>
                                </div>

                                @if ($repairImage)
                                    <div class="relative">
                                        <img src="{{ $repairImage->temporaryUrl() }}" alt="Preview"
                                            class="w-28 h-28 rounded-lg object-cover border">

                                        <button type="button" wire:click="resetImageRepair"
                                            class="btn btn-circle btn-error btn-xs absolute -top-2 -right-2">
                                            ✕
                                        </button>
                                    </div>
                                @endif

                            </div>

                            <label class="label">
                                <span class="label-text-alt">
                                    Format JPG, JPEG, PNG • Maksimal 2 MB
                                </span>
                            </label>

                            @error('repairImage')
                                <span class="text-sm text-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </fieldset>
                </div>

                {{-- Tab 2: Komponen --}}
                <label class="tab">
                    <input type="radio" name="my_tabs_4" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                    </svg>
                    <span class="text-sm font-medium">
                        Komponen
                        @if (count($repairComponents ?? []) > 0)
                            <span class="badge badge-primary badge-sm ml-1">{{ count($repairComponents) }}</span>
                        @endif
                    </span>
                </label>

                <div class="tab-content bg-base-100 border-base-300 p-6">
                    <fieldset class="fieldset">
                        @if ($components)
                            {{-- Form tambah komponen --}}
                            <div class="bg-base-200/50 rounded-lg p-4 mb-5">
                                <p class="font-semibold text-sm mb-3">Tambah Komponen</p>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div class="md:col-span-1">
                                        <label class="label py-1"><span class="label-text">Komponen</span></label>
                                        <select wire:model.live="selectedComponent"
                                            class="select select-bordered w-full select-sm @error('selectedComponent') select-error @enderror">
                                            <option value="">-- Pilih Komponen --</option>
                                            @foreach ($components as $component)
                                                <option value="{{ $component->id_component }}">
                                                    {{ $component->name_component }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedComponent')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label py-1"><span class="label-text">Merk</span></label>
                                        <input type="text" wire:model="merk"
                                            class="input input-bordered input-sm w-full @error('merk') input-error @enderror"
                                            placeholder="Merk">
                                        @error('merk')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label py-1"><span class="label-text">Qty</span></label>
                                        <input type="number" min="1" wire:model="qty"
                                            class="input input-bordered input-sm w-full @error('qty') input-error @enderror"
                                            placeholder="Qty">
                                        @error('qty')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label py-1"><span class="label-text">Harga</span></label>
                                        <input type="number" min="0" wire:model="harga"
                                            class="input input-bordered input-sm w-full @error('harga') input-error @enderror"
                                            placeholder="Harga">
                                        @error('harga')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label py-1"><span class="label-text">Toko</span></label>
                                        <input type="text" wire:model="toko"
                                            class="input input-bordered input-sm w-full @error('store') input-error @enderror"
                                            placeholder="nama toko">
                                        @error('store')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label py-1"><span class="label-text">Teknisi</span></label>
                                        <input type="text" wire:model="technician"
                                            class="input input-bordered input-sm w-full @error('technician') input-error @enderror"
                                            placeholder="nama teknisi">
                                        @error('technician')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label py-1"><span class="label-text">Tanggal
                                                dipasang</span></label>
                                        <input type="date" wire:model="dateInstal"
                                            class="input input-bordered input-sm w-full @error('dateInstal') input-error @enderror"
                                            placeholder="Tanggal dipasang">
                                        @error('dateInstal')
                                            <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex gap-2 mt-4">
                                    <button type="button" wire:click="addComponentItem"
                                        class="btn btn-primary btn-sm">
                                        + Tambah ke Daftar
                                    </button>
                                    <button type="button" wire:click="openModalComponent"
                                        class="btn btn-outline btn-sm">
                                        + Komponen Baru
                                    </button>
                                </div>
                            </div>

                            {{-- Daftar komponen --}}
                            @if (count($repairComponents ?? []) > 0)
                                <div class="overflow-x-auto border rounded-lg" x-data="{
                                    items: @js(
    collect($repairComponents)
        ->map(
            fn($i) => [
                'qty' => (float) ($i['qty'] ?? 0),
                'harga' => (float) ($i['harga'] ?? 0),
            ],
        )
        ->values(),
),
                                    get grandTotal() {
                                        return this.items.reduce((sum, i) => sum + (i.qty * i.harga), 0);
                                    },
                                    formatRupiah(value) {
                                        return 'Rp ' + (value || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
                                    }
                                }">
                                    <table class="table table-sm">
                                        <thead class="bg-base-200">
                                            <tr>
                                                <th>Komponen</th>
                                                <th>Merk</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-right">Harga</th>
                                                <th class="text-right">Subtotal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $grandTotal = 0; @endphp
                                            @foreach ($repairComponents as $index => $item)
                                                @php
                                                    $subtotal = $item['qty'] * $item['harga'];
                                                    $grandTotal += $subtotal;
                                                @endphp
                                                <tr wire:key="repair-item-{{ $index }}" class="hover">
                                                    <td>
                                                        <input type="text"
                                                            wire:model="repairComponents.{{ $index }}.name_component"
                                                            class="input input-sm input-bordered w-full" />
                                                    </td>
                                                    <td>
                                                        <input type="text"
                                                            wire:model="repairComponents.{{ $index }}.merk"
                                                            class="input input-sm input-bordered w-full" />
                                                    </td>
                                                    <td>
                                                        <input type="number" min="1"
                                                            wire:model="repairComponents.{{ $index }}.qty"
                                                            x-model.number="items[{{ $index }}].qty"
                                                            class="input input-sm input-bordered w-20 text-center" />
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0"
                                                            wire:model="repairComponents.{{ $index }}.harga"
                                                            x-model.number="items[{{ $index }}].harga"
                                                            class="input input-sm input-bordered w-28 text-right" />
                                                    </td>
                                                    <td class="text-right font-medium whitespace-nowrap"
                                                        x-text="formatRupiah(items[{{ $index }}].qty * items[{{ $index }}].harga)">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                            wire:click="removeComponentItem({{ $index }})"
                                                            class="btn btn-xs btn-error btn-outline"
                                                            title="Hapus komponen">✕</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-semibold bg-base-200">
                                                <td colspan="4" class="text-right">Total</td>
                                                <td class="text-right whitespace-nowrap">
                                                    <span x-text="formatRupiah(grandTotal)"></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-base-content/50 border rounded-lg border-dashed">
                                    Belum ada komponen ditambahkan.
                                </div>
                            @endif
                        @endif
                    </fieldset>
                </div>

                {{-- Actions --}}

            </div>
            <div class="mt-4 px-2">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="store">
                    <span wire:loading.remove wire:target="store">Simpan</span>
                    <span wire:loading wire:target="store" class="loading loading-spinner loading-md"></span>
                </button>
            </div>
        </form>

        @include('livewire.components.modal-component')
    </div>
</div>
