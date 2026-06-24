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
                    Add Asset
                </span>
            </li>
        </ul>
    </div>
    <div class="flex justify-between items-center mb-4">
        <div class="badge badge-primary">
            <h3 class="font-bold text-lg">Add New Asset</h3>
        </div>
        <button class="btn btn-primary btn-sm" wire:click="$dispatch('openComponentModal')">
            Add Component
        </button>
    </div>
    {{-- control error message --}}
    @if ($errors->any())
    <div role="alert" class="alert alert-error alert-soft mt-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>{{ $errors->first() }}
    </div>
    @endif
    {{-- Tab Menu --}}
    <form wire:submit.prevent="store" class="mt-3" method="POST">
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
            <div class="tab-content bg-base-100 border-base-300 p-6">
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
                        <input type="text" class="input input-accent w-full" wire:model="name" autofocus />
                    </div>
                    <div>
                        <legend class="fieldset-legend">Purchase Date</legend>
                        <input type="date" class="input input-accent w-full" wire:model="purchase_date" />
                    </div>
                    <div class="flex justify-start items-center space-x-3">
                        <div>
                            <legend class="fieldset-legend">Pick Image</legend>
                            <input type="file" accept="image/*" class="file-input" wire:model.live="image" />
                            <label class="label">Max size 2MB</label>
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
                <div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="input input-bordered w-full"
                        placeholder="Cari component...">
    
                    @if($results && $results->isNotEmpty())
                    <div class="menu bg-base-200 rounded-box w-75 z-50">
                        @foreach($results as $item)
                        <div wire:click="selectComponent({{ $item->id_component }})"
                            class="p-2 hover:bg-gray-100 cursor-pointer">
                            {{ $item->name_component }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    {{-- SELECTED --}}
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($components as $id)
                        @php
                        $comp = \App\Models\Component::find($id);
                        @endphp
    
                        <span class="badge badge-primary flex items-center gap-1">
                            {{ $comp->name_component ?? $id }}
    
                            <button type="button" wire:click="removeComponent({{ $id }})" class="ml-1 text-xs">
                                ✕
                            </button>
                        </span>
                        @endforeach
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
            </div>
        </div>
        <div class="m-3">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Save</span>
                <span wire:loading wire:target="store" class="loading loading-spinner loading-md"></span>
            </button>
            <a href="{{ route('assets') }}" type="button" class="btn btn-error">cancel</a>

        </div>
    </form>
    @livewire('components.ModalComponent')
</div>