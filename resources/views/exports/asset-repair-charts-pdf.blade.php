<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grafik Repair Aset</title>
    <style>
        body { font-family: sans-serif; text-align: center; }
        .chart-container { margin-bottom: 30px; }
        .chart-container h3 { margin-bottom: 10px; }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <h2>Laporan Grafik Repair Aset</h2>
    <p>
        @if($startDate && $endDate)
            Periode: {{ $startDate }} - {{ $endDate }}
        @else
            Semua Waktu
        @endif
    </p>

    @if($poinImg)
    <div class="chart-container">
        <h3>Poin Service per Asset</h3>
        <img src="{{ $poinImg }}" alt="Poin Service Chart">
    </div>
    @endif

    @if($biayaImg)
    <div class="chart-container">
        <h3>Biaya Perbaikan per Asset</h3>
        <img src="{{ $biayaImg }}" alt="Biaya Perbaikan Chart">
    </div>
    @endif
</body>
</html>
