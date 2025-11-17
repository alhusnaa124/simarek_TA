<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Rekap PBB per Bagian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }

        h2 {
            margin-top: 30px;
        }

        .bagian-title {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 6px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Rekap PBB per Bagian</h1>
    @if ($filterTahun)
        <p><strong>Tahun:</strong> {{ $filterTahun }}</p>
    @endif

    @php
        // Pisahkan bagian 'Belum Didistribusikan' agar muncul terakhir
        $bagianAkhir = 'Belum Didistribusikan';
        $bagianSorted = collect($grouped)->sortKeys()->reject(fn($_, $key) => $key === $bagianAkhir);
        if ($grouped->has($bagianAkhir)) {
            $bagianSorted = $bagianSorted->merge([$bagianAkhir => $grouped[$bagianAkhir]]);
        }
    @endphp

    @foreach ($bagianSorted as $bagian => $list)
        <h2 class="bagian-title">Bagian: {{ $bagian }}</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Wajib Pajak</th>
                    <th>NOP</th>
                    <th>PBB (Rp)</th>
                    <th>Dharma Tirta (Rp)</th>
                    <th>Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPbb = 0;
                    $totalDharma = 0;
                    $totalJumlah = 0;
                @endphp
                @foreach ($list as $i => $item)
                    @php
                        $pbb = $item->pajak_terhutang ?? 0;
                        $dharma = $item->dharma_tirta ?? 0;
                        $jumlah = $pbb + $dharma;
                        $totalPbb += $pbb;
                        $totalDharma += $dharma;
                        $totalJumlah += $jumlah;
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional($item->wajibPajak)->nama_wp }}</td>
                        <td>{{ $item->nop }}</td>
                        <td>{{ number_format($pbb, 0, ',', '.') }}</td>
                        <td>{{ number_format($dharma, 0, ',', '.') }}</td>
                        <td>{{ number_format($jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total Bagian (Rp)</td>
                    <td>{{ number_format($totalPbb, 0, ',', '.') }}</td>
                    <td>{{ number_format($totalDharma, 0, ',', '.') }}</td>
                    <td>{{ number_format($totalJumlah, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    {{-- Ringkasan Distribusi --}}
    <div style="margin-top: 30px; border: 1px solid #000; padding: 10px;">
        <h4 style="text-align: center; margin-bottom: 10px;">Ringkasan Distribusi PBB Tahun {{ $filterTahun }}</h4>
        <table width="100%" border="1" cellpadding="5" cellspacing="0">
            <tr style="background-color: #f5f5f5;">
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Total Nominal (Rp)</th>
            </tr>
            <tr>
                <td>Sudah Didistribusikan</td>
                <td>{{ $totalDistribusi }}</td>
                <td>{{ number_format($totalNominalDistribusi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Belum Didistribusikan</td>
                <td>{{ $totalBelumDistribusi }}</td>
                <td>{{ number_format($totalNominalBelumDistribusi, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td>Total</td>
                <td>{{ $totalDistribusi + $totalBelumDistribusi }}</td>
                <td>{{ number_format($totalNominalDistribusi + $totalNominalBelumDistribusi, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
