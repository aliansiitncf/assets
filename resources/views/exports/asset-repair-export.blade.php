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

        /* REPORT TABLE */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .report-table thead th {
            background: #ea580c;
            color: #fff;
            text-align: left;
            padding: 7px 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .report-table tbody td {
            padding: 7px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .report-table tbody tr.even {
            background: #f9fafb;
        }

        .report-table .asset-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .report-table .asset-code {
            font-family: monospace;
            font-size: 9.5px;
            color: #6b7280;
        }

        .report-table .badge {
            display: inline-block;
            font-size: 8px;
            padding: 2px 6px;
            background: #ffedd5;
            color: #9a3412;
            border-radius: 10px;
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
                <div class="title">Asset Repair Report</div>
            </td>
            <td class="meta">
                Report ID : RPT-{{ date('Ymd') }}<br>
                Date : {{ $startDate }} to {{ $endDate }}<br>
                Downloaded By : {{ auth()->user()->name }}
            </td>
        </tr>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <h4>Total Asset Repair Recorded</h4>
        <span>{{ count($assetRepairs) }} Units</span>
    </div>

    <!-- TABLE -->
    <table class="report-table" width="100%" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:24px">No</th>
                <th style="width:75px">Kode Aset</th>
                <th>Nama Aset</th>
                <th style="width:90px">Lokasi</th>
                <th style="width:75px">Status</th>
                <th style="width:60px">Out Service</th>
                <th style="width:60px">In Service</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assetRepairs as $assetRepair)
                <tr class="{{ $loop->even ? 'even' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td class="asset-code">{{ $assetRepair->asset->asset_code }}</td>
                    <td>
                        <div class="asset-name">{{ $assetRepair->asset->name }}</div>
                        @if ($assetRepair->asset->category)
                            <span class="badge">{{ strtoupper($assetRepair->asset->category->name) }}</span>
                        @endif
                    </td>
                    <td>{{ optional($assetRepair->asset->latestLocation)->location->name ?? '-' }}</td>
                    <td>{{ $assetRepair->status }}</td>
                    <td>{{ $assetRepair->started_at ? \Carbon\Carbon::parse($assetRepair->started_at)->format('d M Y') : '-' }}
                    </td>
                    <td>{{ $assetRepair->completed_at ? \Carbon\Carbon::parse($assetRepair->completed_at)->format('d M Y') : '-' }}
                    </td>
                    <td>{{ $assetRepair->repair_note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>



    <div class="footer">
        Generated by Asset Management System
    </div>

</body>

</html>
