<div class="modal modal-open">
    <div class="modal-box max-w-4xl ">

        {{-- Header --}}
        <div class="relative flex flex-col md:flex md:flex-row justify-between">
            <div>
                <h3 class="font-bold text-lg">
                    {{ $asset->name }}
                </h3>

                <p class="text-sm opacity-70">
                    Code: {{ $asset->asset_code }}
                </p>
            </div>
            <div class="">
                @if($asset->image_path)
                <img src="{{ Storage::url($asset->image_path) }}" alt="{{ $asset->name }}"
                    class="absolute right-0 top-0 w-32 h-32 md:w-52 md:h-52 object-cover rounded">
                @else
                <div class="w-12 h-12 flex items-center justify-center bg-gray-200 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5z" />
                    </svg>
                </div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="mt-3 space-y-1">
            <p><b>Kategori:</b> {{ $asset->category->name ?? '-' }}</p>
            <p><b>Kondisi:</b> {{ $asset->condition ?? '-' }}</p>
            <h3 class="mt-7 font-bold text-lg">📍 Location Timeline</h3>
            @if (count($locations) > 0)
            <ul class="timeline timeline-snap-icon max-md:timeline-compact timeline-horizontal">
                @foreach($locations as $index => $loc)
                <li>
                    <div class="timeline-middle">
                        <div class="badge badge-secondary">{{ $index + 1 }}</div>
                    </div>
                    <div class="{{ $loop->last ? 'timeline-end' : 'timeline-start' }} timeline-box">
                        <p class="font-semibold text-xs">{{ $loc['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $loc['details'] }}</p>
                        <p class="text-xs text-gray-500">{{ $loc['moved_at'] }}</p>
                    </div>
                    @if (!$loop->last)
                    <hr />
                    @endif
                </li>
                @endforeach
            </ul>
            @else
            <div class="text-center py-6 text-gray-500">
                Tidak ada data perpindahan lokasi.
            </div>
            @endif
        </div>

        {{-- Current Components --}}
        <div class="mt-6">
            <h4 class="font-semibold mb-2">Components Saat Ini</h4>

            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($components as $id)
                @php
                $comp = $asset->components->firstWhere('id_component', $id)
                ?? \App\Models\Component::find($id);
                @endphp

                <span class="badge badge-neutral flex items-center gap-1">
                    {{ $comp->name_component ?? $id }}
                </span>
                @endforeach
            </div>
        </div>
        {{-- Actions --}}
        <div class="modal-action">
            <button wire:click="$dispatch('closeDetailModal')" class="btn btn-accent">
                Close
            </button>
        </div>
    </div>
</div>