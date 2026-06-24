<table>
    <thead>
        <tr>
            <th>Kode Asset</th>
            <th>Gambar</th>
            <th>Nama Asset</th>
            <th>Kategori</th>
            <th>Lokasi Terakhir</th>
            <th>Tanggal Pembelian</th>
            <th>Kondisi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assets as $asset)
            <tr>
                <td>{{ $asset->asset_code }}</td>
                <td></td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->category->name ?? '-' }}</td>
                <td>{{ optional($asset->latestLocation)->location->name ?? '-' }}</td>
                <td>{{ $asset->purchase_date->format('d M Y') }}</td>
                <td>{{ $asset->condition }}</td>
            </tr>
        @endforeach
    </tbody>
</table>