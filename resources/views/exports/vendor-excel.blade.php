<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Vendor</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Jenis Vendor</th>
            <th>Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vendors as $index => $vendor)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $vendor->name }}</td>
                <td>{{ $vendor->address ?? '-' }}</td>
                <td>{{ $vendor->phone ?? '-' }}</td>
                <td>{{ $vendor->type_label }}</td>
                <td>{{ $vendor->created_at->format('d M Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
