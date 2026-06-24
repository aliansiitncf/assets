<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Asset Inventory Report</title>

    <style>
        @page {
            margin: 24px;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            background: #ffffff;
        }

        /* HEADER */
        .header {
            width: 100%;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
        }

        .title {
            font-size: 13px;
            color: #ea580c;
            margin-top: 4px;
        }

        .meta {
            font-size: 11px;
            color: #6b7280;
            text-align: right;
            line-height: 1.6;
        }

        /* SUMMARY */
        .summary {
            background: #ffedd5;
            border-left: 6px solid #ea580c;
            padding: 16px 20px;
            margin-bottom: 22px;
        }

        .summary h4 {
            margin: 0;
            font-size: 11px;
            text-transform: uppercase;
            color: #9a3412;
        }

        .summary span {
            font-size: 22px;
            font-weight: bold;
            color: #ea580c;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
            padding: 8px;
            border: 1px solid #e5e7eb;
        }

        table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .font-semibold {
            font-weight: 600;
        }

        .capitalize {
            text-transform: capitalize;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .text-green-600 {
            color: #16a34a;
        }

        .text-success {
            color: #16a34a;
        }

        .list-disc {
            list-style-type: disc;
        }

        .list-inside {
            list-style-position: inside;
        }

        .ml-4 {
            margin-left: 1rem;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-semibold {
            font-weight: 600;
        }


        .text-center {
            text-align: center;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #000000;
            padding: 10px 0;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table width="100%">
        <tr>
            <td>
                <div class="company">PT Nuclear Coating Fabric</div>
                <div class="title">Audit Log Asset Report</div>
            </td>
            <td class="meta">
                Report ID : RPT-{{ date('Ymd') }}<br>
                Date : {{ date('d F Y') }}<br>
                PIC : {{ auth()->user()->name }}
            </td>
        </tr>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <h4>Audit Log Asset</h4>
    </div>

    <!-- GRID 2 KOLOM -->
    <table width="100%" cellspacing="0" cellpadding="0">
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
                    {{-- log component added to asset --}}
                    @case('component_added')
                    <div>
                        <div class="font-semibold mb-1">Components Added:</div>
                        <ul class="list-disc list-inside">
                            @foreach ($log->properties['components_added'] ?? [] as $component)
                            <li class="text-success">
                                {{ $component['name_component'] ?? '-' }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
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
                    @default
                    <span>No Details</span>
                    @endswitch
                </td>
                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated by Asset Management System
    </div>

</body>

</html>