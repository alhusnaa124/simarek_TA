<style>
    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 1cm;
        }

        body {
            background-color: white !important;
            color: black !important;
        }

        /* Optional: menyembunyikan elemen yang tidak perlu dicetak */
        .no-print {
            display: none !important;
        }
    }
</style>


<body class="bg-gray-100 text-gray-800 font-sans leading-relaxed">
    @vite('resources/css/app.css')

    <div class="container mx-auto p-6 bg-white shadow-md rounded-lg text-sm leading-tight">

        {{-- Kop Surat --}}
        <div class="flex items-center mb-4">
            <img src="/images/logokebumen.jpg" alt="Logo" class="w-16 h-16 mr-4">
            <div class="text-center flex-1">
                <p class="font-bold">PEMERINTAH KABUPATEN KEBUMEN</p>
                <p class="font-bold">DESA WONOYOSO KECAMATAN KUWARASAN</p>
                <p>Jl. Karangbolong KM 2.3 Telp (0287) 473382</p>
            </div>
        </div>

        {{-- Info Wajib Pajak --}}
        <div class="flex justify-between mb-4">
            <div class="w-1/2">
                <p><strong>NAMA:</strong> {{ $kepalaKeluarga->nama_wp ?? '-' }}</p>
                <p><strong>ALAMAT:</strong> {{ $kepalaKeluarga->alamat_wp ?? '-' }}</p>
            </div>
        </div>

        <hr class="border border-black mb-4">

        {{-- Tabel Detail PBB --}}
        <table class="min-w-full border-collapse border border-black mb-4 text-xs">
            <thead class="text-center font-bold">
                <tr>
                    <th class="border border-black px-2 py-1">NO</th>
                    <th class="border border-black px-2 py-1">NOP</th>
                    <th class="border border-black px-2 py-1">NAMA</th>
                    <th class="border border-black px-2 py-1">LUAS</th>
                    <th class="border border-black px-2 py-1">PBB</th>
                    <th class="border border-black px-2 py-1">D. Tirta</th>
                    <th class="border border-black px-2 py-1">JUMLAH</th>
                    <th class="border border-black px-2 py-1">KET</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @php
                    $totalLuas = 0;
                    $totalPbb = 0;
                    $totalTarik = 0;
                @endphp
                @foreach ($wajibPajaks as $i => $wp)
                    @foreach ($wp->pbb as $pbb)
                        @php
                            $jumlah = $pbb->pajak_terhutang + $pbb->dharma_tirta;
                            $totalLuas += $pbb->luas_tnh;
                            $totalPbb += $pbb->pajak_terhutang;
                            $totalTarik += $pbb->dharma_tirta;
                        @endphp
                        <tr>
                            <td class="border border-black px-2 py-1">{{ $i+1 }}</td>
                            <td class="border border-black px-2 py-1">{{ $pbb->nop }}</td>
                            <td class="border border-black px-2 py-1">{{ $wp->nama_wp }}</td>
                            <td class="border border-black px-2 py-1">{{ $pbb->luas_tnh }}</td>
                            <td class="border border-black px-2 py-1">{{ number_format($pbb->pajak_terhutang) }}</td>
                            <td class="border border-black px-2 py-1">{{ number_format($pbb->dharma_tirta) }}</td>
                            <td class="border border-black px-2 py-1">{{ number_format($jumlah) }}</td>
                            <td class="border border-black px-2 py-1">-</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="font-bold">
                    <td colspan="3" class="border border-black px-2 py-1 text-center">JUMLAH</td>
                    <td class="border border-black px-2 py-1">{{ $totalLuas }}</td>
                    <td class="border border-black px-2 py-1">{{ number_format($totalPbb) }}</td>
                    <td class="border border-black px-2 py-1">{{ number_format($totalTarik) }}</td>
                    <td class="border border-black px-2 py-1">{{ number_format($totalPbb + $totalTarik) }}</td>
                    <td class="border border-black px-2 py-1">-</td>
                </tr>
            </tbody>
        </table>

        {{-- Tanda tangan Kepala Desa --}}
        <div class="flex justify-between items-start mt-20">
            <div class="flex flex-col">
                <label class="font-bold mb-1">Jadwal Pembayaran:</label>
                <span class="border border-gray-300 rounded px-2 py-1 w-48 bg-gray-100">
                {{ \Carbon\Carbon::parse($formulir->jadwal_pembayaran)->translatedFormat('d F Y') ?? '-' }}
                </span>
            </div>

            <div class="text-center">
                <p>Wonoyoso, {{ date('d-m-Y') }}</p>
                <p><strong>KEPALA DESA WONOYOSO</strong></p>
                <img src="/images/ttd.png" alt="Tanda Tangan" class="w-32 mt-2">
                <p><strong>IMAM MASKURI</strong></p>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
