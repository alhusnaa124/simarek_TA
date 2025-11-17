@extends('layout')

@section('content')
    {{-- Alert Success --}}
    @if (session('success'))
        <div id="alert-success"
            class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

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

        {{-- Pilih Kelompok dan Filter Tahun --}}
        <div class="mb-4 flex justify-end gap-2">
            {{-- Dropdown Kelompok --}}
            <form id="kelompokForm">
                <select onchange="location = this.value;" class="form-select border rounded p-2">
                    <option value="{{ route('formulir') }}">-- Pilih kelompok --</option>
                    @foreach ($kelompokBelumAdaFormulir as $k)
                        @foreach ($k->tahun_belum_ada_formulir as $tahun)
                            @php
                                $kepala = $k->wajibPajak->firstWhere('kepala_keluarga', true);
                                $bagian = $kepala?->bagian;
                                $alamatSingkat = $kepala
                                    ? implode(' ', array_slice(explode(' ', $kepala->alamat_wp), 0, 4))
                                    : '-';
                                $url = route('formulir.show', $k->id) . '?tahun=' . $tahun;
                            @endphp
                            <option value="{{ $url }}"
                                {{ isset($kelompok, $tahunDipilih) && $kelompok->id == $k->id && $tahunDipilih == $tahun ? 'selected' : '' }}>
                                {{ $k->nama_kelompok }}{{ $alamatSingkat ? ' - ' . $alamatSingkat : '' }}
                                -  {{ $tahun }}
                            </option>
                        @endforeach
                    @endforeach

                </select>
            </form>

            {{-- Dropdown Tahun --}}
            {{-- @if (isset($kelompok))
                <form method="GET">
                    <input type="hidden" name="id" value="{{ $kelompok->id }}">
                    <select name="tahun" onchange="this.form.submit()" class="form-select border rounded p-2">
                        <option value="">-- Semua Tahun --</option>
                        @php
                            $tahunList = $wajibPajaks->flatMap->pbb->pluck('tahun')->unique()->sortDesc();
                        @endphp
                        @foreach ($tahunList as $thn)
                            <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>
                                {{ $thn }}</option>
                        @endforeach
                    </select>
                </form>
            @endif --}}
        </div>


        @isset($kelompok)
            {{-- Info Wajib Pajak --}}
            <div class="flex justify-between mb-4">
                <div class="w-1/2">
                    <p><strong>NAMA:</strong> {{ $kepalaKeluarga->nama_wp ?? '-' }}</p>
                    <p><strong>ALAMAT:</strong> {{ $kepalaKeluarga->alamat_wp ?? '-' }}</p>
                </div>
            </div>


            <hr class="border border-black mb-4">

            {{-- Tabel --}}
            <table class="min-w-full border-collapse border border-black mb-4 text-xs">
                <thead class="text-center font-bold">
                    <tr>
                        <th class="border border-black px-2 py-1">NO</th>
                        <th class="border border-black px-2 py-1">Tahun</th>
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
                        @foreach ($wp->pbb->sortBy('tahun')->filter(fn($pbb) => request('tahun') ? $pbb->tahun == request('tahun') : true) as $pbb)
                            @php
                                $jumlah = $pbb->pajak_terhutang + $pbb->dharma_tirta;
                                $totalLuas += $pbb->luas_tnh;
                                $totalPbb += $pbb->pajak_terhutang;
                                $totalTarik += $pbb->dharma_tirta;
                            @endphp
                            <tr>
                                <td class="border border-black px-2 py-1">{{ $loop->iteration }}</td>
                                <td class="border border-black px-2 py-1">{{ $pbb->tahun }}</td>
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
                        <td colspan="4" class="border border-black px-2 py-1 text-center">JUMLAH</td>
                        <td class="border border-black px-2 py-1">{{ $totalLuas }}</td>
                        <td class="border border-black px-2 py-1">{{ number_format($totalPbb) }}</td>
                        <td class="border border-black px-2 py-1">{{ number_format($totalTarik) }}</td>
                        <td class="border border-black px-2 py-1">{{ number_format($totalPbb + $totalTarik) }}</td>

                        <td class="border border-black px-2 py-1">-</td>
                    </tr>
                </tbody>
            </table>

            @php
                $config = json_decode(Storage::get('public/formulir_config.json'), true);
            @endphp

            {{-- Form Simpan --}}
            <form action="{{ route('formulir.store') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="id_kelompok" value="{{ $kelompok->id }}">
                <input type="hidden" name="total" value="{{ $totalPbb + $totalTarik }}">
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">


                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <label for="jadwal_pembayaran" class="font-bold mb-1">Jadwal Pembayaran:</label>
                        <input type="date" name="jadwal_pembayaran" id="jadwal_pembayaran"
                            class="border rounded px-2 py-1 w-48" value="{{ $config['jadwal_pembayaran'] ?? '' }}" required>
                    </div>
                    <div class="text-center">
                        <p>Wonoyoso, {{ date('d-m-Y') }}</p>
                        <p><strong>KEPALA DESA WONOYOSO</strong></p>

                        @if (!empty($config['ttd_kepala_desa']) && file_exists(public_path('ttd/' . $config['ttd_kepala_desa'])))
                            <img src="{{ asset('ttd/' . $config['ttd_kepala_desa']) }}" alt="Tanda Tangan" class="w-32 mt-2">
                        @else
                            <p class="italic text-gray-500 mt-2">TTD belum diatur</p>
                        @endif

                        <p><strong>{{ $config['nama_kepalaDesa'] ?? '' }}</strong></p>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <div>
                        <a href="{{ route('config.formulir') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium text-sm rounded-lg transition duration-150 ease-in-out">
                            Edit Pengaturan
                        </a>
                    </div>
                    <div>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition duration-150 ease-in-out">
                            Simpan Formulir
                        </button>
                    </div>
                </div>
            </form>
        @endisset
    </div>

    <div class="mt-3">@include('formulirTarikan.tarikan', ['tahunDipilih' => $tahunDipilih])
    </div>
@endsection
