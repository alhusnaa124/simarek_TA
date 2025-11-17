@extends('layout')

@section('content')
    <style>
        div.dataTables_filter {
            margin-bottom: 1rem;
        }
    </style>

    {{-- Filter Tahun dan Export --}}
    <form method="GET" class="mb-6 bg-white p-4 rounded-xl shadow-md">
        <div class="flex flex-wrap md:flex-nowrap items-end gap-4">
            {{-- Dropdown Tahun --}}
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="border px-3 py-2 rounded-lg w-40">
                    <option value="">Semua Tahun</option>
                    @foreach ($daftarTahun as $tahun)
                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Filter --}}
            <div>
                <label class="block text-sm font-medium text-transparent mb-1">.</label>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Filter
                </button>
            </div>

            {{-- Tombol Reset --}}
            @if (request('tahun'))
                <div>
                    <label class="block text-sm font-medium text-transparent mb-1">.</label>
                    <a href="{{ route('rekapPetugas') }}"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        Reset
                    </a>
                </div>
            @endif

            {{-- Tombol Export --}}
            <div class="flex flex-wrap gap-2 md:ml-auto">


                @if (request('tahun'))
                    <a href="{{ route('rekap.export.bagian', ['tahun' => request('tahun')]) }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Export Rekap Per Bagian
                    </a>
                    <a href="{{ route('rekap.export-wp', ['status' => 'lunas', 'tahun' => request('tahun')]) }}"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Export Lunas
                    </a>

                    <a href="{{ route('rekap.export-wp', ['status' => 'belum', 'tahun' => request('tahun')]) }}"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition">
                        Export Belum Lunas
                    </a>
                @else
                    <button type="button" onclick="alert('Silakan pilih tahun terlebih dahulu')"
                        class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                        Export Rekap Per Bagian
                    </button>
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
    </form>

    <div class="container mx-auto mt-5">
        <div class="bg-white p-6 rounded-xl shadow-lg space-y-10">
            {{-- Rekap PBB per Bagian --}}
            <div>
                <h1 class="text-2xl font-bold mb-4">REKAP PBB PER BAGIAN </h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">Bagian</th>
                                <th class="py-3 px-6">SPPT</th>
                                <th class="py-3 px-6">Pajak Terhutang (Rp)</th>
                                <th class="py-3 px-6">Dharma Tirta(Rp)</th>
                                <th class="py-3 px-6">Jumlah(Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekapWilayah as $index => $wilayah)
                                <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                                    <td class="py-3 px-6">{{ $wilayah->bagian }}</td>
                                    <td class="py-3 px-6">{{ $wilayah->sppt }}</td>
                                    <td class="py-3 px-6">{{ number_format($wilayah->pajak_terhutang, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6">{{ number_format($wilayah->dharma_tirta, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6">{{ number_format($wilayah->jumlah, 2, ',', '.') }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-green-100">
                                <td colspan="3" class="py-3 px-6 text-right">TOTAL (Rp)</td>
                                <td class="py-3 px-6">
                                    {{ number_format($rekapWilayah->where('ikut_total', true)->sum('pajak_terhutang'), 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-6">
                                    {{ number_format($rekapWilayah->where('ikut_total', true)->sum('dharma_tirta'), 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-6">
                                    {{ number_format($rekapWilayah->where('ikut_total', true)->sum('jumlah'), 2, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Rekap Pembayaran Lunas --}}
            <div>
                <h1 class="text-2xl font-bold mb-4 mt-10">REKAP PEMBAYARAN LUNAS</h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">ID Formulir</th>
                                <th class="py-3 px-6">Total Pembayaran (Rp)</th>
                                <th class="py-3 px-6">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapLunas as $index => $formulir)
                                <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                                    <td class="py-3 px-6">{{ $formulir->id }}</td>
                                    <td class="py-3 px-6">{{ number_format($formulir->total, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6">
                                        @if ($formulir->bukti)
                                            <a href="{{ asset('storage/' . $formulir->bukti) }}" target="_blank"
                                                class="text-blue-500 underline">Lihat Bukti</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center">Tidak ada data lunas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($rekapLunas->count())
                            <tfoot>
                                <tr class="font-bold bg-green-100">
                                    <td colspan="2" class="py-3 px-6 text-right">TOTAL SEMUA (Rp)</td>
                                    <td class="py-3 px-6">{{ number_format($rekapLunas->sum('total'), 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Rekap Setoran Petugas --}}
            <div>
                <h1 class="text-2xl font-bold mb-4 mt-10">REKAP SETORAN {{ $user->nama }}</h1>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">Tanggal Setor</th>
                                <th class="py-3 px-6">Jumlah Setoran (Rp)</th>
                                <th class="py-3 px-6">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($setoranPetugas as $index => $setoran)
                                <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                                    <td class="py-3 px-6">
                                        {{ \Carbon\Carbon::parse($setoran->tanggal_setor)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6">{{ number_format($setoran->jumlah_setor ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-6">{{ $setoran->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center">Belum ada setoran.</td>
                                </tr>
                            @endforelse
                            @if ($setoranPetugas->count())
                        <tfoot>
                            <tr class="font-bold bg-green-100">
                                <td colspan="2" class="py-3 px-6 text-right">TOTAL SETORAN (Rp)</td>
                                <td class="py-3 px-6" colspan="2">
                                    {{ number_format($setoranPetugas->sum('jumlah_setor'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
