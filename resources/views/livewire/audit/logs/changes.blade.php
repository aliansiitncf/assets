@if ($log->properties && $log->properties->has('changes'))

    <div class="space-y-3">

        @foreach ($log->properties['changes'] as $field => $change)
            <div class="border rounded-lg p-3">

                <div class="font-semibold capitalize mb-2">
                    {{ str_replace('_', ' ', $field) }}
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">

                    <div>
                        <div class="font-medium text-error">
                            Before
                        </div>

                        {{ is_array($change['before']) || is_object($change['before'])
                            ? json_encode($change['before'])
                            : ($change['before'] ?:
                                '-') }}
                    </div>

                    <div>
                        <div class="font-medium text-success">
                            After
                        </div>

                        {{ is_array($change['after']) || is_object($change['after'])
                            ? json_encode($change['after'])
                            : ($change['after'] ?:
                                '-') }}
                    </div>

                </div>

            </div>
        @endforeach

    </div>

@endif
