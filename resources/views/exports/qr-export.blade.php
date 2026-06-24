<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Print QR Label</title>

<style>
@page {
    size: {{ $pageSetup->width }}mm {{ $pageSetup->height }}mm;
    margin: {{ $pageSetup->gap_vertical }}mm {{ $pageSetup->gap_horizontal }}mm;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
}

/* ROW */
.row {
    display: grid;
    grid-template-columns: repeat({{ $pageSetup->column }}, 1fr);
    gap: {{ $pageSetup->gap_vertical }}mm {{ $pageSetup->gap_horizontal }}mm;
    
}

/* LABEL */
.label {
    display: flex;
    align-items: center;
    justify-content: center;
    width: {{ ($pageSetup->width - ($pageSetup->gap_horizontal * ($pageSetup->column + 1))) / $pageSetup->column }}mm;
    height: {{ $pageSetup->height - ($pageSetup->gap_vertical * 2) }}mm;
}

.label-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    max-width: 90%;
    max-height: 90%;
}

.qr {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    flex: 1;
}
/* QR */
.qr svg {
    width: 75%;
    text-align: center;
    height: auto;
    display: block;
    max-width: 100%;
    max-height: 100%;
}

/* TEXT */
.code {
    font-size: 6pt;
    margin-top: 2mm;
    text-align: center;
    white-space: nowrap;
}

/* PRINT SAFETY */
@media print {
    .row {
        page-break-inside: avoid;
    }
}
</style>
</head>

<body onload="window.print()">

@foreach ($assets as $asset)
<div class="row">
    @for ($i = 0; $i < $pageSetup->column; $i++)
        <div class="label">
            <div class="label-inner">
                <div class="qr">
                    {!! QrCode::size(500)->margin(0)->generate($asset->asset_code) !!}
                </div>
                <div class="code">
                    {{ $asset->asset_code }}
                </div>
            </div>
        </div>
    @endfor
</div>
@endforeach

</body>
</html>