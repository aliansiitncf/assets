@props(['field', 'sortField', 'sortDirection'])
<th wire:click="sortBy({{ "'" . $field . "'" }})" class="cursor-pointer">
    <div class="flex items-center gap-1">
        <span>{{ $slot }}</span>
        <div>
            @if ($sortField === $field)
            @if ($sortDirection === 'asc')
            <x-sort-asc />
            @else
            <x-sort-desc />
            @endif
            @endif
        </div>
    </div>
</th>