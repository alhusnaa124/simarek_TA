<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran PBB</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #333; padding: 20px; margin: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; }
        .line { border-top: 1px dashed #888; margin: 10px 0; }
        .content table { width: 100%; }
        .content td { padding: 4px 0; vertical-align: top; }
        .content td:first-child { width: 140px; }
        .footer { margin-top: 40px; text-align: center; }
        .footer p { margin: 0; }
        .signature-line { margin-top: 60px; border-top: 1px solid #000; width: 200px; margin: 60px auto 0; }
        .signature-name { margin-top: 6px; font-weight: bold; }
        .daftar-wp { margin-top: 20px; }
        .daftar-wp table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .daftar-wp th, .daftar-wp td { border: 1px solid #999; padding: 6px; text-align: left; font-size: 11px; }
        .daftar-wp th { background-color: #f5f5f5; font-weight: bold; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
        .nowrap { white-space: nowrap; }
    </style>
</head>

<body>
    <div class="header">
        <h2>Kwitansi Pembayaran PBB</h2>
        <p>Desa Wonoyoso, Kecamatan Kuwarasan</p>
    </div>

    <div class="line"></div>

    <div class="content">
        <table>
            <tr>
                <td><strong>ID Formulir</strong></td>
                <td>:
                    @if($pembayaran->formulir_ids ?? false)
                        {{ $pembayaran->formulir_ids }}
                    @else
                        {{ $pembayaran->id }}
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Nama Kelompok</strong></td>
                <td>: {{ $pembayaran->kelompok->nama_kelompok ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Pembayaran</strong></td>
                <td>: {{ $pembayaran->tgl_bayar ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tahun Dibayar</strong></td>
                <td>:
                    @php
                        $tahunUnik = collect($pembayaran->pbb)->pluck('tahun')->unique()->sort();
                    @endphp
                    {{ $tahunUnik->count() > 1 ? $tahunUnik->first().' - '.$tahunUnik->last() : $tahunUnik->first() }}
                </td>
            </tr>
            <tr>
                <td><strong>Total PBB</strong></td>
                <td>: Rp {{ number_format($pembayaran->total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="daftar-wp">
            <strong>Daftar Wajib Pajak:</strong>
            <table>
                <thead>
                    <tr>
                        <th class="center">No</th>
                        <th class="center">Tahun</th>
                        <th class="center">NOP</th>
                        <th>Nama</th>
                        <th class="right">Pajak Terhutang</th>
                        <th class="right">Dharma Tirta</th>
                        <th class="right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPbb = 0;
                        $totalTarik = 0;
                        $grandTotal = 0;
                        $no = 1;
                    @endphp

                    @foreach ($pembayaran->pbb->sortBy(['tahun', 'nop']) as $pbb)
                        @php
                            $jumlah = $pbb->pajak_terhutang + $pbb->dharma_tirta;
                            $totalPbb += $pbb->pajak_terhutang;
                            $totalTarik += $pbb->dharma_tirta;
                            $grandTotal += $jumlah;
                        @endphp
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td class="center">{{ $pbb->tahun }}</td>
                            <td class="nowrap">{{ $pbb->nop }}</td>
                            <td>{{ $pbb->wajibPajak->nama_wp ?? '-' }}</td>
                            <td class="right">{{ number_format($pbb->pajak_terhutang, 0, ',', '.') }}</td>
                            <td class="right">{{ number_format($pbb->dharma_tirta, 0, ',', '.') }}</td>
                            <td class="right">{{ number_format($jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    <tr class="total-row">
                        <td colspan="4" class="right"><strong>JUMLAH</strong></td>
                        <td class="right"><strong>{{ number_format($totalPbb, 0, ',', '.') }}</strong></td>
                        <td class="right"><strong>{{ number_format($totalTarik, 0, ',', '.') }}</strong></td>
                        <td class="right"><strong>{{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        <div class="signature-line"></div>
        <p class="signature-name">{{ $pembayaran->petugas->nama ?? '-' }}</p>
    </div>
</body>
</html>
