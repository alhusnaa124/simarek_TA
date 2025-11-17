<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Setoran</title>
    <style>
        body { font-family: sans-serif; padding: 30px; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .content { border: 1px solid #000; padding: 20px; }
        .footer { margin-top: 30px; text-align: right; }
        table { width: 100%; }
        td { padding: 5px; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Kwitansi Pembayaran Setoran</h2>
        <p>Desa/Kelurahan {{ config('app.name') }}</p>
    </div>

    <div class="content">
        <table>
            <tr>
                <td><strong>Nomor Kwitansi</strong></td>
                <td>: #{{ $setoran->id }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Setor</strong></td>
                <td>: {{ \Carbon\Carbon::parse($setoran->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Nama Petugas</strong></td>
                <td>: {{ $setoran->petugas->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Setor</strong></td>
                <td>: Rp {{ number_format($setoran->jumlah_setor, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>: {{ $setoran->status }}</td>
            </tr>
            <tr>
                <td><strong>Catatan</strong></td>
                <td>: {{ $setoran->catatan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p> {{ \Carbon\Carbon::parse($setoran->tanggal)->translatedFormat('d F Y') }}</p>
        <p><strong>Bendahara</strong></p>
        <br><br><br>
        <p>________________________</p>
    </div>
</body>
</html>
