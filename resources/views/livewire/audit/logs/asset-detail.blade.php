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
                @continue(($before[$key] ?? null) == ($after[$key] ?? null))

                <div class="grid grid-cols-2 gap-3 mb-2">

                    <div>
                        <div class="text-error text-sm">
                            Before {{ ucfirst($key) }}
                        </div>

                        {{ $before[$key] ?? '-' }}
                    </div>

                    <div>
                        <div class="text-success text-sm">
                            After {{ ucfirst($key) }}
                        </div>

                        {{ $after[$key] ?? '-' }}
                    </div>

                </div>
            @endforeach

        </div>
    @endforeach

</div>
