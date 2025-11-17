<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Export Wajib Pajak {{ ucfirst($status) }} - {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DAFTAR WAJIB PAJAK {{ strtoupper($status) }}</h1>
        <p>Tahun: {{ $tahun }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    @php
        $totalPajak = 0;
        $totalDharma = 0;
        $totalKeseluruhan = 0;

        function renderTableHeader() {
            return '
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Wajib Pajak</th>
                            <th>NOP</th>
                            <th>Alamat</th>
                            <th class="text-right">Pajak Terhutang</th>
                            <th class="text-right">Dharma Tirta</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            ';
        }

        echo renderTableHeader();
    @endphp

    @forelse ($pbbList as $index => $pbb)
        @php
            $pajakTerhutang = $pbb->pajak_terhutang;
            $dharmaTirta = $pbb->dharma_tirta;
            $jumlah = $pajakTerhutang + $dharmaTirta;

            $totalPajak += $pajakTerhutang;
            $totalDharma += $dharmaTirta;
            $totalKeseluruhan += $jumlah;
        @endphp

        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $pbb->wajibPajak->nama_wp ?? '-' }}</td>
            <td>{{ $pbb->nop ?? '-' }}</td>
            <td>{{ $pbb->wajibPajak->alamat_wp ?? '-' }}</td>
            <td class="text-right">{{ number_format($pajakTerhutang, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($dharmaTirta, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($jumlah, 0, ',', '.') }}</td>
        </tr>

        @if ($loop->iteration % 35 == 0 && !$loop->last)
            </tbody>
            </table>
            <div style="page-break-after: always;"></div>
            {!! renderTableHeader() !!}
        @endif
    @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data</td>
        </tr>
    @endforelse

    <tr style="background-color: #f0f0f0; font-weight: bold;">
        <td colspan="4" class="text-center">TOTAL</td>
        <td class="text-right">{{ number_format($totalPajak, 0, ',', '.') }}</td>
        <td class="text-right">{{ number_format($totalDharma, 0, ',', '.') }}</td>
        <td class="text-right">{{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
    </tr>

    </tbody>
    </table>

    <div style="margin-top: 30px;">
        <p>Total Data: {{ $pbbList->count() }} record</p>
    </div>
</body>

</html>
