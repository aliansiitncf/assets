@php
    $before = $log->properties['before'] ?? [];
    $after = $log->properties['after'] ?? [];

    $keys = collect(array_keys($before))->merge(array_keys($after))->unique();
@endphp

<div class="space-y-2">

    @foreach ($keys as $key)
        @continue(($before[$key] ?? null) == ($after[$key] ?? null))

        <div class="border rounded-lg p-3">

            <div class="font-semibold mb-2">
                {{ $key }}
            </div>

            <div class="grid grid-cols-2 gap-3">

                <div>

                    <div class="text-error text-sm">
                        Before
                    </div>

                    {{ $before[$key] ?? '-' }}

                </div>

                <div>

                    <div class="text-success text-sm">
                        After
                    </div>

                    {{ $after[$key] ?? '-' }}

                </div>

            </div>

        </div>
    @endforeach

</div>
