<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Formulir Tarikan PBB</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            position: relative;
            display: flex;
            align-items: center;
            height: 70px;
            margin-bottom: 20px;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin-right: 15px;
            z-index: 2;
        }

        /* Teks tetap di tengah halaman, sejajar vertikal dengan logo */
        .header-text {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            line-height: 1.2;
        }

        .header-text p:first-child,
        .header-text p:nth-child(2) {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .header-text p:last-child {
            font-size: 11px;
        }


        /* Info Wajib Pajak */
        .info-section {
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-item strong {
            font-weight: bold;
        }

        /* Tabel */
        .table-container {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .jadwal-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .jadwal-section {
            flex: 1;
            text-align: left;
            /* Pastikan teks jadwal tetap kiri */
        }

        .signature-section {
            flex: 1;
            text-align: right;
            /* Pastikan signature di kanan */
        }

        .signature-section p {
            margin-bottom: 5px;
        }

        .signature-section .title {
            font-weight: bold;
        }

        .signature-image {
            width: 120px;
            height: auto;
            margin: 15px 0;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Responsive untuk PDF */
        @media print {
            body {
                font-size: 11px;
            }

            .container {
                padding: 15px;
            }

            table {
                font-size: 9px;
            }

            th,
            td {
                padding: 4px 3px;
            }
        }

        /* Flexbox fallback untuk PDF engine */
        .flex {
            display: table;
            width: 100%;
        }

        .flex-item {
            display: table-cell;
            vertical-align: top;
        }

        .flex-item-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/logokebumen.jpg') }}" alt="Logo" class="logo" />
            <div class="header-text">
                <p>PEMERINTAH KABUPATEN KEBUMEN</p>
                <p>DESA WONOYOSO KECAMATAN KUWARASAN</p>
                <p>Jl. Karangbolong KM 2.3 Telp (0287) 473382</p>
            </div>
        </div>

        <div class="info-section">
            <div><strong>NAMA:</strong> {{ $kepalaKeluarga->nama_wp ?? '-' }}</div>
            <div><strong>ALAMAT:</strong> {{ $kepalaKeluarga->alamat_wp ?? '-' }}</div>
        </div>

        <div class="table-container">
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tahun</th>
                        <th>NOP</th>
                        <th>Nama WP</th>
                        <th>Luas Tanah</th>
                        <th>Pajak Terhutang</th>
                        <th>Dharma Tirta</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                        $totalLuas = 0;
                        $totalPajak = 0;
                        $totalDharma = 0;
                    @endphp
                    @foreach ($pbbs as $pbb)
                        @php
                            $jumlah = $pbb->pajak_terhutang + $pbb->dharma_tirta;
                            $totalLuas += $pbb->luas_tnh;
                            $totalPajak += $pbb->pajak_terhutang;
                            $totalDharma += $pbb->dharma_tirta;
                        @endphp
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $pbb->tahun }}</td>
                            <td>{{ $pbb->nop }}</td>
                            <td>{{ $pbb->wajibPajak->nama_wp ?? '-' }}</td>
                            <td>{{ $pbb->luas_tnh }}</td>
                            <td>{{ number_format($pbb->pajak_terhutang, 0, ',', '.') }}</td>
                            <td>{{ number_format($pbb->dharma_tirta, 0, ',', '.') }}</td>
                            <td>{{ number_format($jumlah, 0, ',', '.') }}</td>
                            <td>-</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>{{ $totalLuas }}</strong></td>
                        <td><strong>{{ number_format($totalPajak, 0, ',', '.') }}</strong></td>
                        <td><strong>{{ number_format($totalDharma, 0, ',', '.') }}</strong></td>
                        <td><strong>{{ number_format($totalPajak + $totalDharma, 0, ',', '.') }}</strong></td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div>
                Jadwal Pembayaran: {{ \Carbon\Carbon::parse($formulir->jadwal_pembayaran)->translatedFormat('d F Y') ?? '-' }}
            </div>
            <div style="text-align:right;">
                <p>Wonoyoso, {{ date('d-m-Y') }}</p>
                <p><strong>KEPALA DESA WONOYOSO</strong></p>
                <img src="{{ public_path('images/ttd.png') }}" alt="Tanda Tangan" style="width:120px;" />
                <p><strong><u>IMAM MASKURI</u></strong></p>
            </div>
        </div>
    </div>
</body>

</html>
