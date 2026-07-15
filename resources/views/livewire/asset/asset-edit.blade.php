<div>
    <div class="breadcrumbs text-sm">
        <ul>
            <li>
                <a href="{{ route('assets') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="h-4 w-4 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    Assets
                </a>
            </li>
            <li>
                <span class="inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="h-4 w-4 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Edit Asset
                </span>
            </li>
        </ul>
    </div>
    <div class="flex justify-between">
        <div class="badge badge-primary">
            <h3 class="font-bold text-lg">Edit Asset</h3>
        </div>
        <button class="btn btn-primary btn-md" wire:click="$dispatch('openComponentModal')">
            <x-heroicon-o-plus-circle class="h-5 w-5" />
            Komponen Baru
        </button>
    </div>
    {{-- control error message --}}
    @if ($errors->any())
        <div role="alert" class="alert alert-error alert-soft mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>{{ $errors->first() }}
        </div>
    @endif
    {{-- Tab Menu --}}
    <form wire:submit.prevent="update" method="post">
        <div class="tabs tabs-lift shadow-md rounded-2xl mt-3">
            {{-- Asset --}}
            <label class="tab">
                <input type="radio" name="my_tabs_4" checked="checked" />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 me-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5" />
                </svg>
                General Asset
            </label>
            <div class="tab-content bg-base-100 shadow-md border-base-300 p-6">
                <fieldset class="fieldset">
                    <div>
                        <legend class="fieldset-legend">Category</legend>
                        <select class="select select-accent w-full" wire:model="category_id">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id_category }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <legend class="fieldset-legend">Asset Code</legend>
                        <input type="text" class="input input-accent w-full" wire:model="asset_code" disabled />
                    </div>
                    <div>
                        <legend class="fieldset-legend">Name</legend>
                        <input type="text" class="input input-accent w-full" wire:model="name" />
                    </div>
                    <div>
                        <legend class="fieldset-legend">Purchase Date</legend>
                        <input type="date" class="input input-accent w-full" wire:model="purchase_date" />
                    </div>
                    <div class="flex justify-start items-center space-x-3">
                        <div>
                            <legend class="fieldset-legend">Pick Image</legend>
                            <input type="file" class="file-input" wire:model.live="image" />
                            <label class="label">Max size 2MB</label>
                            {{-- error --}}
                            @error('image')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                            {{-- Spinner saat loading --}}
                            <span wire:loading wire:target="image"
                                class="loading loading-spinner loading-md ml-2 mt-2"></span>
                        </div>
                        {{-- preview image --}}
                        @if ($image)
                            <div class="mt-2">
                                <img src="{{ $image->temporaryUrl() }}" alt="Image Preview"
                                    class="w-32 h-32 object-cover rounded">
                            </div>
                            <button type="button" class="btn btn-sm mt-2 btn-error"
                                wire:click="resetFieldImage">Remove</button>
                        @endif
                    </div>
                </fieldset>
            </div>
            {{-- End Asset --}}
            {{-- Component --}}
            <label class="tab">
                <input type="radio" name="my_tabs_4" />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 me-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                </svg>
                Components
            </label>
            <div class="tab-content bg-base-100 border-base-300 p-6">
                <div x-data="{ open: @entangle('isOpen') }" x-on:click.outside="open = false; $wire.closeDropdown()" class="relative">
                    {{-- Kotak select2-like: chip terpilih + input search jadi satu --}}
                    <div x-on:click="$refs.searchInput.focus(); if (!open) $wire.openDropdown()"
                        class="flex flex-wrap items-center gap-1 min-h-[42px] w-full pl-2 pr-8 py-1 border border-gray-300 rounded-md bg-white cursor-text transition-colors focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
                        @foreach ($this->selectedComponents as $comp)
                            <span wire:key="selected-{{ $comp->id_component }}"
                                class="flex items-center gap-1 bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded px-2 py-0.5">
                                {{ $comp->name_component }}
                                <button type="button" wire:click.stop="removeComponent({{ $comp->id_component }})"
                                    class="text-gray-400 hover:text-gray-700 leading-none text-xs">
                                    &times;
                                </button>
                            </span>
                        @endforeach

                        <input type="text" x-ref="searchInput" wire:model.live.debounce.300ms="search"
                            x-on:click.stop="if (!open) $wire.openDropdown()"
                            placeholder="{{ $this->selectedComponents->isEmpty() ? 'Cari component...' : '' }}"
                            class="flex-1 min-w-[100px] border-none outline-none focus:ring-0 text-sm py-0.5 bg-transparent"
                            autocomplete="off">

                        {{-- Panah kecil ala select --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="open" x-cloak x-transition
                        class="absolute w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-64 overflow-y-auto">
                        @forelse($results as $item)
                            <div wire:click="selectComponent({{ $item->id_component }})"
                                wire:key="component-option-{{ $item->id_component }}"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-blue-500 hover:text-white">
                                {{ $item->name_component }}
                            </div>
                        @empty
                            <div class="px-3 py-2 text-sm text-gray-400">Tidak ada hasil</div>
                        @endforelse
                    </div>
                </div>
            </div>
            {{-- Detail --}}
            <label class="tab">
                <input type="radio" name="my_tabs_4" />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 me-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6-2.292m0-14.25v14.25" />
                </svg>
                Detail
            </label>
            <div class="tab-content bg-base-100 border-base-300 p-6">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold">Detail Asset</h3>
                    </div>

                    <div class="space-y-2">
                        @foreach ($detailItems as $index => $item)
                            <div wire:key="detail-item-{{ $index }}" class="flex gap-2 items-start">
                                <div class="flex-1">
                                    <input type="text" wire:model="detailItems.{{ $index }}.name"
                                        class="input input-bordered input-sm w-full" placeholder="Nama (mis: Warna)">
                                    @error("detailItems.$index.name")
                                        <span class="text-error text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex-1">
                                    <input type="text" wire:model="detailItems.{{ $index }}.value"
                                        class="input input-bordered input-sm w-full" placeholder="Nilai (mis: Hitam)">
                                    @error("detailItems.$index.value")
                                        <span class="text-error text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="button" wire:click="removeDetailItem({{ $index }})"
                                    class="btn btn-sm btn-ghost text-error" title="Hapus baris">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addDetailItem" class="btn btn-sm btn-outline">
                            + Tambah Detail
                        </button>
                    </div>
                </div>
            </div>

            {{-- End Components --}}
            {{-- Location --}}
            <label class="tab">
                <input type="radio" name="my_tabs_4" />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 me-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                Location
            </label>
            <div class="tab-content bg-base-100 border-base-300 p-6">
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <button type="button" wire:click="addLocation" class="btn btn-sm btn-neutral">
                            Move Location
                        </button>
                    </div>
                </div>
                @if ($locationForm)
                    <div>
                        <legend class="fieldset-legend">Location</legend>
                        <select wire:model="location_id" class="select select-accent w-full">
                            <option value="">Select Location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id_location }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <legend class="fieldset-legend">Detail Location</legend>
                        <input type="text" class="input input-accent w-full" wire:model="details"
                            placeholder="Detail Location" />
                    </div>
                    <div>
                        <legend class="fieldset-legend">Date</legend>
                        <input type="date" class="input input-accent w-full" wire:model="moved_at" />
                    </div>
                @endif
            </div>
            {{-- End Location --}}
        </div>
        <div class="m-3">
            <button type="submit" class="btn btn-primary">
                <span wire:loading.remove>Save</span>
                <span wire:loading wire:target="update" class="loading loading-spinner loading-md"></span>
            </button>
            <a href="{{ route('assets') }}" type="button" class="btn btn-error">cancel</a>
        </div>
    </form>
    @livewire('components.ModalComponent')
</div>
