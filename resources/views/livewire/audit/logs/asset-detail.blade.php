@php
    $changes = $log->properties['changes'] ?? [];
@endphp

<div class="space-y-2">

    @foreach ($changes as $change)
        @php
            $before = $change['before'] ?? [];
            $after = $change['after'] ?? [];

            $keys = collect(array_keys($before))->merge(array_keys($after))->unique();
        @endphp

        <div class="border rounded-lg p-3">

            <div class="font-semibold mb-2">
                {{ $before['name'] ?? ($after['name'] ?? 'Detail') }}
            </div>

            @foreach ($keys as $key)
                @continue(($before[$key] ?? null) === ($after[$key] ?? null))

                <div class="flex items-center gap-2 text-sm">
                    <span class="font-medium capitalize">{{ $key }}:</span>

                    <span class="text-error">
                        {{ $before[$key] ?? '-' }}
                    </span>

                    <span>→</span>

                    <span class="text-success">
                        {{ $after[$key] ?? '-' }}
                    </span>
                </div>
            @endforeach

        </div>
    @endforeach

</div>
