<table>
    {{-- Detail Asset Section --}}
    <tr>
        <td><strong>Kode Asset</strong></td>
        <td>{{ $asset->asset_code }}</td>
    </tr>
    <tr>
        <td><strong>Nama Asset</strong></td>
        <td>{{ $asset->name }}</td>
    </tr>
    @if($asset->details->count() > 0)
        <tr>
            <td><strong>Detail</strong></td>
            <td>{{ $asset->details->map(fn($d) => $d->name . ': ' . $d->pivot->value)->implode('; ') }}</td>
        </tr>
    @endif

    {{-- Empty row separator --}}
    <tr><td></td></tr>

    {{-- Table Header --}}
    <tr>
        <th>No</th>
        <th>Out of Service</th>
        <th>HM/KM</th>
        <th>Trouble Description</th>
        <th>Action Taken</th>
        <th>Tanggal Pemasangan Part</th>
        <th>Part/Komponen</th>
        <th>Merk</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Vendor Toko</th>
        <th>Vendor Teknisi</th>
        <th>In Service</th>
    </tr>

    {{-- Data Rows --}}
    @php $no = 1; @endphp
    @foreach ($repairs as $repair)
        @php
            $components = $repair->components;
            $rowCount = max($components->count(), 1);

            // Jika repair sudah di-link ke damage spesifik, gunakan itu
            // Jika belum (data lama), fallback ke semua damage notes milik asset
            $troubleDescription = $repair->damage
                ? $repair->damage->damage_note
                : $asset->damages->pluck('damage_note')->filter()->implode('; ');
            $troubleDescription = $troubleDescription ?: '-';
        @endphp

        @if ($components->isEmpty())
            {{-- Repair tanpa komponen --}}
            <tr>
                <td>{{ $no }}</td>
                <td>{{ optional($repair->started_at)->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $repair->hm_km ?? '-' }}</td>
                <td>{{ $troubleDescription }}</td>
                <td>{{ $repair->repair_note ?? '-' }}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>{{ optional($repair->completed_at)->format('d/m/Y') ?? '-' }}</td>
            </tr>
        @else
            @foreach ($components as $index => $component)
                <tr>
                    @if ($index === 0)
                        {{-- Baris pertama: data repair + komponen pertama --}}
                        <td>{{ $no }}</td>
                        <td>{{ optional($repair->started_at)->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $repair->hm_km ?? '-' }}</td>
                        <td>{{ $troubleDescription }}</td>
                        <td>{{ $repair->repair_note ?? '-' }}</td>
                    @else
                        {{-- Baris berikutnya: kolom repair kosong --}}
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif

                    {{-- Kolom komponen (selalu terisi) --}}
                    <td>{{ $component->pivot->date ? \Carbon\Carbon::parse($component->pivot->date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $component->name_component }}</td>
                    <td>{{ $component->pivot->merk ?? '-' }}</td>
                    <td>{{ $component->pivot->qty }}</td>
                    <td>{{ number_format($component->pivot->price, 0, ',', '.') }}</td>
                    <td>{{ optional($component->pivot->vendor)->name ?? '-' }}</td>
                    <td>{{ optional($component->pivot->technician)->name ?? '-' }}</td>

                    @if ($index === 0)
                        <td>{{ optional($repair->completed_at)->format('d/m/Y') ?? '-' }}</td>
                    @else
                        <td></td>
                    @endif
                </tr>
            @endforeach
        @endif

        @php $no++; @endphp
    @endforeach
</table>
