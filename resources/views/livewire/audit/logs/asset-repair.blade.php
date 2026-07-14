@php
    $components = $log->properties['components'] ?? [];
@endphp

@if (!empty($components['added']))
    <div class="mb-3">
        <strong class="text-success">Komponen Ditambahkan</strong>
        <ul class="list-disc list-inside">
            @foreach ($components['added'] as $component)
                <li>{{ $component }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (!empty($components['updated']))
    <div class="space-y-3">
        <strong class="text-warning">Komponen Diubah</strong>

        @foreach ($components['updated'] as $update)
            <div>
                <div class="font-semibold">{{ $update['component'] }}</div>

                @foreach ($update['after'] as $field => $after)
                    @php($before = $update['before'][$field] ?? null)

                    @continue($before == $after)

                    <div class="ml-4">
                        <strong>{{ ucfirst($field) }}</strong> :
                        <span class="text-error">{{ $before ?: '0' }}</span>
                        →
                        <span class="text-success">{{ $after ?: '0' }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif
