@foreach ($log->properties as $key => $value)
    <div class="mb-1">
        <span class="font-semibold">
            {{ ucfirst(str_replace('_', ' ', $key)) }} :
        </span>

        @if (is_array($value) || is_object($value))
            {{ json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
        @else
            {{ $value ?: '-' }}
        @endif
    </div>
@endforeach
