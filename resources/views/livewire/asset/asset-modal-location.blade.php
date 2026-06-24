@if ($showLocationModal)
<div class="modal modal-open">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg">📍 Location Timeline</h3>
        @if (count($locations) > 0)
        <ul class="timeline timeline-snap-icon max-md:timeline-compact timeline-horizontal">
            @foreach($locations as $index => $loc)
            <li>
                <div class="timeline-middle">
                    <div class="badge badge-primary">{{ $index + 1 }}</div>
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

        <div class="modal-action">
            <button wire:click="$set('showLocationModal', false)" class="btn">Close</button>
        </div>
    </div>
</div>
@endif