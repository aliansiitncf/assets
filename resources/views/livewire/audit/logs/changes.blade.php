@if ($log->properties && $log->properties->has('changes'))

    <div class="space-y-3">

        @foreach ($log->properties['changes'] as $field => $change)
            <div class="p-3">

                <div class="font-semibold capitalize mb-2">
                    {{ str_replace('_', ' ', $field) }}
                </div>

                @php
                    $format = fn($v) => is_array($v) || is_object($v) ? json_encode($v) : ($v ?: '-');
                @endphp

                <div class="text-sm space-y-1">
                    <div><span class="text-error">Before:</span> {{ $format($change['before']) }}</div>
                    <div><span class="text-success">After:</span> {{ $format($change['after']) }}</div>
                </div>

            </div>
        @endforeach

    </div>

@endif
