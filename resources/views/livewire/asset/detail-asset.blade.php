<div class="modal modal-open">
    <div class="modal-box max-w-4xl" x-data="{ activeTab: 'timeline' }">

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
                @if ($asset->image_path)
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

        {{-- Info dasar tetap tampil di atas tab --}}
        <div class="mt-3 space-y-1">
            <p><b>Kategori:</b> {{ $asset->category->name ?? '-' }}</p>
            <p><b>Kondisi:</b>
                <span
                    class="badge {{ match ($asset->condition) {
                        'Baik' => 'badge-success',
                        'Rusak' => 'badge-error',
                        default => '',
                    } }}">
                    {{ $asset->condition ?? '-' }}
                </span>
            </p>
        </div>

        {{-- Tabs --}}
        <div role="tablist" class="tabs tabs-lift mt-6">
            <a role="tab" class="tab" :class="activeTab === 'timeline' && 'tab-active font-semibold'"
                @click="activeTab = 'timeline'">
                📍 Location Timeline
            </a>
            <a role="tab" class="tab" :class="activeTab === 'detail' && 'tab-active font-semibold'"
                @click="activeTab = 'detail'">
                📋 Detail Tambahan
            </a>
            <a role="tab" class="tab" :class="activeTab === 'components' && 'tab-active font-semibold'"
                @click="activeTab = 'components'">
                🔧 Components
            </a>
        </div>

        <div class="mt-4 min-h-[200px]">

            {{-- Tab: Location Timeline --}}
            <div x-show="activeTab === 'timeline'">
                @if (count($locations) > 0)
                    <ul class="timeline timeline-snap-icon max-md:timeline-compact timeline-horizontal">
                        @foreach ($locations as $index => $loc)
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

            {{-- Tab: Detail Tambahan --}}
            <div x-show="activeTab === 'detail'" x-cloak>
                @if ($asset->details->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="table table-xs border-collapse border border-gray-200 w-full">
                            <tbody>
                                @foreach ($asset->details as $detail)
                                    <tr>
                                        <td class="font-semibold uppercase">{{ $detail->name }} :</td>
                                        <td>{{ $detail->pivot->value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500 text-sm">
                        Tidak ada detail tambahan.
                    </div>
                @endif
            </div>

            {{-- Tab: Components --}}
            <div x-show="activeTab === 'components'" x-cloak>
                <div class="flex flex-wrap gap-2">
                    @forelse($asset->components as $comp)
                        <span class="badge badge-neutral flex items-center gap-1">
                            {{ $comp->name_component }}
                        </span>
                    @empty
                        <span class="text-sm text-gray-400">Tidak ada component.</span>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Actions --}}
        <div class="modal-action">
            <button type="button" wire:click="close" class="btn btn-accent">
                Close
            </button>
        </div>
    </div>
</div>
