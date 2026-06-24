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

        /* ASSET CARD */
        .asset {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .asset-image {
            width: 110px;
            height: 110px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: #f9fafb;
            text-align: center;
            vertical-align: middle;
        }

        .asset-image img {
            width: 110px;
            height: 110px;
        }

        .asset-code {
            font-size: 10px;
            font-family: monospace;
            color: #000000;
        }

        .asset-name {
            font-size: 13px;
            font-weight: bold;
            margin: 4px 0 6px;
        }

        .badge {
            display: inline-block;
            font-size: 9px;
            padding: 3px 8px;
            background: #ea580c;
            color: #fff;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        .asset-info {
            font-size: 11px;
            line-height: 1.6;
        }

        .asset-info span {
            color: #6b7280;
            display: inline-block;
            width: 70px;
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
                <div class="title">Asset Inventory Report</div>
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
        <h4>Total Asset Repair Recorded</h4>
        <span>{{ count($assetRepairs) }} Units</span>
    </div>

    <!-- GRID 2 KOLOM -->
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            @foreach($assetRepairs as $index => $assetRepair)
            <td width="50%" valign="top" style="padding:6px">

                <div class="asset">
                    <table width="100%">
                        <tr>
                            <!-- IMAGE -->
                            <td width="110" valign="top">
                                <div class="asset-image">
                                    @if($assetRepair->image_path && file_exists(public_path('storage/' . $assetRepair->image_path)))
                                    <img src="{{ public_path('storage/' . $assetRepair->image_path) }}">
                                    @else
                                    <div style="font-size:10px;color:#aaa;margin-top:45px">No Image</div>
                                    @endif
                                </div>
                            </td>

                            <!-- CONTENT -->
                            <td valign="top" style="padding-left:12px">
                                <div class="asset-code">{{ $assetRepair->asset->asset_code }}</div>
                                <div class="asset-name">{{ $assetRepair->asset->name }}</div>

                                @if($assetRepair->asset->category)
                                <div class="badge">{{ strtoupper($assetRepair->asset->category->name) }}</div>
                                @endif

                                <div class="asset-info">
                                    <div><span>Notes</span>:
                                        {{ $assetRepair->repair_note }}</div>
                                    <div><span>Location</span>:
                                        {{ optional($assetRepair->asset->latestLocation)->location->name ?? '-' }}</div>
                                    <div><span>Status</span>: {{ $assetRepair->asset->condition }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

            </td>

            @if(($index + 1) % 2 == 0)
        </tr>
        <tr>
            @endif
            @endforeach
        </tr>
    </table>

    <div class="footer">
        Generated by Asset Management System
    </div>

</body>

</html>