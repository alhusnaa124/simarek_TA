@extends('layout')

@section('content')
    <style>
        div.dataTables_filter {
            margin-bottom: 1rem;
        }
    </style>

    <div class="container mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg space-y-10">

        {{-- Filter --}}
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <form action="{{ route('rekap') }}" method="GET" class="flex flex-wrap gap-3 items-center" id="filterForm">
                {{-- Filter Tahun --}}
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700">Filter Tahun</label>
                    <select name="tahun" id="tahun"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Tahun</option>
                        @foreach ($daftarTahun as $th)
                            <option value="{{ $th }}" {{ request('tahun') == $th ? 'selected' : '' }}>
                                {{ $th }}</option>
                        @endforeach
                    </select>
                </div>


                {{-- Filter Petugas --}}
                <div>
                    <label for="petugas" class="block text-sm font-medium text-gray-700">Filter Petugas</label>
                    <select name="petugas" id="petugas"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Petugas</option>
                        @foreach ($petugasList as $petugas)
                            <option value="{{ $petugas->id }}" {{ request('petugas') == $petugas->id ? 'selected' : '' }}>
                                {{ $petugas->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tanggal Setoran --}}
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Setor Dari</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Sampai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                        value="{{ request('tanggal_selesai') }}"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                </div>

                {{-- Filter Rekap --}}
                <div>
                    <label for="filter_rekap" class="block text-sm font-medium text-gray-700">Jenis Rekap</label>
                    <select name="filter_rekap" id="filter_rekap"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="semua" {{ request('filter_rekap') == 'semua' ? 'selected' : '' }}>Semua Rekap
                        </option>
                        <option value="pbb_wilayah" {{ request('filter_rekap') == 'pbb_wilayah' ? 'selected' : '' }}>Rekap
                            PBB Per Petugas</option>
                        <option value="setoran" {{ request('filter_rekap') == 'setoran' ? 'selected' : '' }}>Rekap Setoran
                        </option>
                    </select>
                </div>

                <div class="pt-5 flex gap-2">
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Filter</button>
                    <a href="{{ route('rekap') }}"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-600 transition whitespace-nowrap">
                        Reset Filter
                    </a>
                </div>

            </form>

            {{-- Tombol Cetak PDF --}}
            <div class="pt-5">
                <button type="button" onclick="cetakPDF()"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition whitespace-nowrap flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Cetak PDF
                </button>
            </div>
            <div class="pt-5 flex gap-2">
                @if (request('tahun'))
                    <a href="{{ route('rekap.export-wp', ['status' => 'lunas', 'tahun' => request('tahun')]) }}"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Lunas
                    </a>

                    <a href="{{ route('rekap.export-wp', ['status' => 'belum', 'tahun' => request('tahun')]) }}"
                        class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Belum Lunas
                    </a>
                @else
                    <button type="button" onclick="alert('Silakan pilih tahun terlebih dahulu')"
                        class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                        Export Lunas
                    </button>
                    <button type="button" onclick="alert('Silakan pilih tahun terlebih dahulu')"
                        class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                        Export Belum Lunas
                    </button>
                @endif
            </div>

        </div>

        {{-- Rekap PBB Per Petugas --}}
        @if (request('filter_rekap') == 'semua' || request('filter_rekap') == 'pbb_wilayah')
            <div>
                <h2 class="text-2xl font-bold mb-4 text-green-800">Rekap PBB Per Petugas</h2>
                <div class="overflow-x-auto">
                    <table id="tabelBagian" class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">Nama Petugas</th>
                                <th class="py-3 px-6">Bagian</th>
                                <th class="py-3 px-6">Jumlah SPPT</th>
                                <th class="py-3 px-6">Pajak Terhutang (Rp)</th>
                                <th class="py-3 px-6">Dharma Tirta (Rp)</th>
                                <th class="py-3 px-6">Jumlah Keseluruhan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($rekapWilayah->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data PBB ditemukan.
                                    </td>
                                </tr>
                            @else
                                @foreach ($rekapWilayah as $petugas)
                                    @foreach ($petugas->bagian_detail as $i => $bagian)
                                        <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                            <td class="py-3 px-6">{{ $loop->iteration }}</td>
                                            <td class="py-3 px-6">{{ $petugas->nama_petugas }}</td>
                                            <td class="py-3 px-6">{{ $bagian->bagian }}</td>
                                            <td class="py-3 px-6">{{ number_format($bagian->sppt, 0, ',', '.') }}</td>
                                            <td class="py-3 px-6">
                                                {{ number_format($bagian->pajak_terhutang, 0, ',', '.') }}</td>
                                            <td class="py-3 px-6">{{ number_format($bagian->dharma_tirta, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-6">{{ number_format($bagian->jumlah, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    {{-- Total per petugas --}}
                                    <tr class="bg-green-200 font-semibold">
                                        <td colspan="3" class="py-3 px-6 text-center">Total
                                            {{ $petugas->nama_petugas }}
                                        </td>
                                        <td class="py-3 px-6">{{ number_format($petugas->total_sppt, 0, ',', '.') }}</td>
                                        <td class="py-3 px-6">
                                            {{ number_format($petugas->total_pajak_terhutang, 0, ',', '.') }}</td>
                                        <td class="py-3 px-6">
                                            {{ number_format($petugas->total_dharma_tirta, 0, ',', '.') }}</td>
                                        <td class="py-3 px-6">{{ number_format($petugas->total_jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Rekap Setoran --}}
        @if (request('filter_rekap') == 'semua' || request('filter_rekap') == 'setoran')
            <div class="mt-10">
                <h2 class="text-2xl font-bold mb-4 text-green-800">Total Setoran per Petugas</h2>
                <div class="overflow-x-auto">
                    <table id="tabelSetoran" class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-800 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">Nama Petugas</th>
                                <th class="py-3 px-6">Tanggal Setor</th>
                                <th class="py-3 px-6">Total Setoran (Rp)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($setoranPerPetugas->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada data setoran
                                        ditemukan.</td>
                                </tr>
                            @else
                                @foreach ($setoranPerPetugas as $petugas)
                                    @foreach ($petugas->detail_setoran as $i => $item)
                                        <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                            <td class="py-3 px-6">{{ $loop->iteration }}</td>
                                            <td class="py-3 px-6">{{ $petugas->nama_petugas }}</td>
                                            <td class="py-3 px-6">{{ $item->tanggal_setor }}</td>
                                            <td class="py-3 px-6">{{ number_format($item->jumlah_setor, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    {{-- Total per petugas --}}
                                    <tr class="bg-green-200 font-semibold">
                                        <td colspan="3" class="py-3 px-6 text-center">Total Setoran
                                            {{ $petugas->nama_petugas }}</td>
                                        <td class="py-3 px-6">{{ number_format($petugas->total_setoran, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Ringkasan Total Semua Data --}}
        <div class="bg-blue-100 p-6 rounded-xl shadow-inner mt-10">
            <h2 class="text-2xl font-bold mb-6 text-blue-900">Ringkasan Semua Data (Termasuk yang Belum Didistribusikan)
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-blue-900 font-semibold text-lg">
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total SPPT</div>
                    <div class="text-2xl">{{ number_format($totalSemuaSppt, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Pajak Terhutang (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalSemuaPajakTerhutang, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Dharma Tirta (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalSemuaDharmaTirta, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Jumlah Keseluruhan (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalSemuaJumlah, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Lunas (Rp)</div>
                    <div class="text-2xl text-green-600">{{ number_format($totalSemuaLunas, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md col-span-2 md:col-span-2">
                    <div>Selisih Belum Lunas (Rp)</div>
                    <div class="text-3xl text-red-600 font-bold">{{ number_format($totalSemuaSelisih, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="text-center mt-6 text-xl font-bold text-gray-800">
                Persentase Distribusi: {{ number_format($persenDistribusi, 2) }}%
            </div>
        </div>


        {{-- Ringkasan Total Keseluruhan --}}
        <div class="bg-green-100 p-6 rounded-xl shadow-inner mt-10">
            <h2 class="text-2xl font-bold mb-6 text-green-900">Ringkasan Total Yang Udah Di distribusikan</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-green-900 font-semibold text-lg">
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total SPPT</div>
                    <div class="text-2xl">{{ number_format($totalSppt, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Pajak Terhutang (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalPajakTerhutang, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Dharma Tirta (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalDharmaTirta, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Jumlah Keseluruhan (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalJumlah, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div>Total Setoran (Rp)</div>
                    <div class="text-2xl">{{ number_format($totalSetoran, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-md col-span-2 md:col-span-2">
                    <div>Selisih (Rp)</div>
                    <div class="text-3xl text-red-600 font-bold">{{ number_format($selisih, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fungsi untuk cetak PDF
        function cetakPDF() {
            // Ambil nilai dari form filter
            const petugas = document.getElementById('petugas').value;
            const tanggalMulai = document.getElementById('tanggal_mulai').value;
            const tanggalSelesai = document.getElementById('tanggal_selesai').value;
            const filterRekap = document.getElementById('filter_rekap').value;

            // Buat URL dengan parameter
            const params = new URLSearchParams();
            if (petugas) params.append('petugas', petugas);
            if (tanggalMulai) params.append('tanggal_mulai', tanggalMulai);
            if (tanggalSelesai) params.append('tanggal_selesai', tanggalSelesai);
            if (filterRekap) params.append('filter_rekap', filterRekap);

            // Buka PDF di tab baru
            const url = '{{ route('rekap.cetak-pdf') }}?' + params.toString();
            window.open(url, '_blank');
        }
    </script>
@endpush
