<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap PBB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2d5a2d;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #2d5a2d;
            font-size: 20px;
            margin: 0;
        }

        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
            font-weight: normal;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2d5a2d;
        }

        .filter-info h3 {
            margin: 0 0 8px 0;
            color: #2d5a2d;
            font-size: 14px;
        }

        .filter-info p {
            margin: 3px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #2d5a2d;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .table-title {
            color: #2d5a2d;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }

        .number {
            text-align: right;
        }

        .total-row {
            background-color: #e8f5e8;
            font-weight: bold;
        }

        .summary {
            background-color: #f0f8f0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 2px solid #2d5a2d;
        }

        .summary h3 {
            color: #2d5a2d;
            margin: 0 0 15px 0;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .summary-item {
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2d5a2d;
        }

        .summary-item.selisih .value {
            color: #d32f2f;
            font-size: 16px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }

        .even-row {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>REKAP PAJAK BUMI DAN BANGUNAN (PBB)</h1>
        <h2>Laporan Rekapitulasi</h2>
    </div>

    <div class="filter-info">
        <h3>Informasi Filter:</h3>
        <p><strong>Petugas:</strong> {{ $namaPetugas }}</p>
        <p><strong>Jenis Rekap:</strong>
            @if ($filterRekap == 'semua')
                Semua Rekap
            @elseif($filterRekap == 'pbb_wilayah')
                Rekap PBB Per Wilayah
            @elseif($filterRekap == 'setoran')
                Rekap Setoran
            @endif
        </p>
        @if ($filterTanggalMulai || $filterTanggalSelesai)
            <p><strong>Periode:</strong>
                {{ $filterTanggalMulai ? date('d/m/Y', strtotime($filterTanggalMulai)) : 'Tidak ditentukan' }} -
                {{ $filterTanggalSelesai ? date('d/m/Y', strtotime($filterTanggalSelesai)) : 'Tidak ditentukan' }}
            </p>
        @endif
        <p><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</p>
    </div>

    {{-- Rekap PBB Per Wilayah --}}
    @if ($filterRekap == 'semua' || $filterRekap == 'pbb_wilayah')
        <div class="table-title">Rekap PBB Per Wilayah</div>
        @if ($rekapWilayah->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 25%">Petugas</th>
                        <th style="width: 20%">Bagian</th>
                        <th style="width: 10%">Jumlah SPPT</th>
                        <th style="width: 15%">Pajak Terhutang (Rp)</th>
                        <th style="width: 15%">Dharma Tirta (Rp)</th>
                        <th style="width: 15%">Jumlah Keseluruhan (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekapWilayah as $index => $item)
                        <tr class="">
                            <td style="text-align: center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_petugas }}</td>
                            <td>{{ $item->bagian }}</td>
                            <td class="number">{{ number_format($item->sppt, 0, ',', '.') }}</td>
                            <td class="number">{{ number_format($item->pajak_terhutang, 0, ',', '.') }}</td>
                            <td class="number">{{ number_format($item->dharma_tirta, 0, ',', '.') }}</td>
                            <td class="number">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data rekap PBB per wilayah untuk filter yang dipilih.
            </div>
        @endif
    @endif

    {{-- Rekap Setoran --}}
    @if ($filterRekap == 'semua' || $filterRekap == 'setoran')
        @if ($filterRekap == 'semua' && $rekapWilayah->count() > 0)
            <div class="page-break"></div>
        @endif

        <div class="table-title">Rekap Setoran per Petugas</div>
        @if ($setoran->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 30%">Nama Petugas</th>
                        <th style="width: 20%">Tanggal Setor</th>
                        <th style="width: 20%">Total Setoran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedSetoran = $setoran->groupBy('id_petugas');
                        $counter = 1;
                    @endphp

                    @foreach ($groupedSetoran as $idPetugas => $group)
                        @php
                            $namaPetugas = optional($group->first()->petugas)->nama ?? 'Tidak Diketahui';
                            $totalPetugas = $group->sum('jumlah_setor');
                        @endphp

                        @foreach ($group as $i => $item)
                            <tr class="{{ $counter % 2 == 0 ? 'even-row' : '' }}">
                                <td style="text-align: center">{{ $counter }}</td>
                                <td>{{ $namaPetugas }}</td>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal_setor)) }}</td>
                                <td class="number">{{ number_format($item->jumlah_setor, 0, ',', '.') }}</td>
                            </tr>
                            @php $counter++; @endphp
                        @endforeach

                        <tr class="total-row">
                            <td colspan="3" style="text-align: center; font-weight: bold;">
                                Total Setoran {{ $namaPetugas }}
                            </td>
                            <td class="number" style="font-weight: bold;">
                                {{ number_format($totalPetugas, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data setoran untuk filter yang dipilih.
            </div>
        @endif
    @endif

    {{-- Ringkasan Total --}}
    <div class="summary">
        <h3>Ringkasan Total Keseluruhan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total SPPT</div>
                <div class="value">{{ number_format($totalSppt, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Pajak Terhutang (Rp)</div>
                <div class="value">{{ number_format($totalPajakTerhutang, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Dharma Tirta (Rp)</div>
                <div class="value">{{ number_format($totalDharmaTirta, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Jumlah Keseluruhan (Rp)</div>
                <div class="value">{{ number_format($totalJumlah, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Setoran (Rp)</div>
                <div class="value">{{ number_format($totalSetoran, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item selisih">
                <div class="label">Selisih (Rp)</div>
                <div class="value">{{ number_format($selisih, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
        <p>Laporan ini dibuat secara otomatis oleh sistem</p>
    </div>
</body>

</html>
