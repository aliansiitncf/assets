<div class="w-full">

    <h1 class="text-2xl font-bold">Logs Page</h1>
    <div class="flex justify-between items-center mb-4">
        <div class="my-4 flex items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search Log..."
                class="input input-bordered w-full" />
            <select wire:model.live="perPage" class="select select-bordered w-36">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
            </select>
        </div>
        <div>
            <button wire:click="refresh" wire:loading.attr="disabled" type="button" class="btn btn-primary">
                <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span wire:loading wire:target="refresh" class="loading loading-spinner loading-md"></span>
            </button>
            <button class="btn btn-secondary" wire:click="openModalPDF">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                    <path d="M17 18h2" />
                    <path d="M20 15h-3v6" />
                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1" />
                </svg>
            </button>
        </div>
    </div>


    <div class="overflow-x-auto shadow-md rounded-box border border-base-content/5 mb-4">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Event</th>
                    <th>Deskripsi</th>
                    <th>Detail</th>
                    <th>Waktu</th>

                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>
                        {{ $log->causer?->name ?? 'System' }}
                    </td>
                    <td>{{ $log->log_name }}</td>
                    <td>{{ $log->description }}</td>
                    <td>
                        @switch($log->event)
                        {{-- log create asset --}}
                        @case('asset_created')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log update asset --}}
                        @case('asset_updated')
                        @if ($log->properties && $log->properties->has('changes'))
                        <ul>
                            @foreach($log->properties['changes'] as $field => $change)
                            <li>
                                <span class="font-semibold capitalize">{{str_replace('_', ' ', $field)}}</span>:
                                <div class="ml-4">
                                    <span class="text-red-600">Before:
                                        {{ is_array($change['before']) || is_object($change['before']) ? json_encode($change['before']) : $change['before'] }}</span><br>
                                    <span class="text-green-600">After:
                                        {{ is_array($change['after']) || is_object($change['after']) ? json_encode($change['after']) : $change['after'] }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @break
                        {{-- log delete asset --}}
                        @case('asset_deleted')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log location moved from asset --}}
                        @case('location_moved')
                        <div>
                            <div class="font-semibold mb-1">Location Moved Details:</div>
                            <ul class="list-disc list-inside">
                                @foreach ($log->properties as $key => $value)
                                <li>
                                    <span class="font-semibold capitalize">{{str_replace('_', ' ', $key)}}</span>:
                                    {{ is_array($value) || is_object($value) ? json_encode($value) : $value }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @break
                        {{-- log category created --}}
                        @case('category_created')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log category updated --}}
                        @case('category_updated')
                        @if ($log->properties && $log->properties->has('changes'))
                        <ul>
                            @foreach($log->properties['changes'] as $field => $change)
                            <li>
                                <span class="font-semibold capitalize">{{str_replace('_', ' ', $field)}}</span>:
                                <div class="ml-4">
                                    <span class="text-red-600">Before:
                                        {{ is_array($change['before']) || is_object($change['before']) ? json_encode($change['before']) : $change['before'] }}</span><br>
                                    <span class="text-green-600">After:
                                        {{ is_array($change['after']) || is_object($change['after']) ? json_encode($change['after']) : $change['after'] }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @break
                        {{-- log category deleted --}}
                        @case('category_deleted')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log component created --}}
                        @case('component_created')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log component updated --}}
                        @case('component_updated')
                        @if ($log->properties && $log->properties->has('changes'))
                        <ul>
                            @foreach($log->properties['changes'] as $field => $change)
                            <li>
                                <span class="font-semibold capitalize">{{str_replace('_', ' ', $field)}}</span>:
                                <div class="ml-4">
                                    <span class="text-red-600">Before:
                                        {{ is_array($change['before']) || is_object($change['before']) ? json_encode($change['before']) : $change['before'] }}</span><br>
                                    <span class="text-green-600">After:
                                        {{ is_array($change['after']) || is_object($change['after']) ? json_encode($change['after']) : $change['after'] }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @break
                        {{-- log component deleted --}}
                        @case('component_deleted')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log location created --}}
                        @case('location_created')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- log location updated --}}
                        @case('location_updated')
                        @if ($log->properties && $log->properties->has('changes'))
                        <ul>
                            @foreach($log->properties['changes'] as $field => $change)
                            <li>
                                <span class="font-semibold capitalize">{{str_replace('_', ' ', $field)}}</span>:
                                <div class="ml-4">
                                    <span class="text-red-600">Before:
                                        {{ is_array($change['before']) || is_object($change['before']) ? json_encode($change['before']) : $change['before'] }}</span><br>
                                    <span class="text-green-600">After:
                                        {{ is_array($change['after']) || is_object($change['after']) ? json_encode($change['after']) : $change['after'] }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @break
                        {{-- log location deleted --}}
                        @case('location_deleted')
                        @foreach($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- asset damaged --}}
                        @case('asset_damaged')
                        @foreach ($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- asset damaged --}}
                        @case('asset_repaired')
                        @foreach ($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        {{-- asset damaged --}}
                        @case('asset_repair_completed')
                        @foreach ($log->properties as $key => $value)
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                            @if(is_array($value) || is_object($value))
                            {{ json_encode($value) }}
                            @else
                            {{ $value }}
                            @endif
                        </div>
                        @endforeach
                        @break
                        @default
                        <span class="italic text-gray-400">No Details</span>
                        @endswitch
                    </td>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">
            {{ $logs->links() }}
        </div>

    </div>
    @include('livewire.audit.audit-log-modalPDF')
</div>