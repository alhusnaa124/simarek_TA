@extends('layout')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Detail Pembayaran PBB</h2>

            {{-- Informasi Pembayaran --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                <div>
                    <p class="font-medium">Nama Petugas:</p>
                    <p class="text-gray-900">{{ $formulir->petugas->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-medium">ID Formulir:</p>
                    <p class="text-gray-900">
                        {{ $formulir->is_gabungan ? $formulir->formulir_ids : $formulir->id }}
                    </p>
                </div>
                <div>
                    <p class="font-medium">Tanggal Bayar:</p>
                    <p class="text-gray-900">{{ $formulir->tgl_bayar ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-medium">Nama Kelompok:</p>
                    <p class="text-gray-900">{{ $formulir->kelompok->nama_kelompok ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-medium">Tahun Dibayar:</p>
                    <p class="text-gray-900">
                        {{ $formulir->is_gabungan ? $formulir->tahun_gabungan : $formulir->tahun }}
                    </p>
                </div>
                <div>
                    <p class="font-medium">Total PBB:</p>
                    <p class="text-gray-900">
                        Rp {{ number_format($formulir->total, 0, ',', '.') }}
                    </p>
                </div>

                @if ($formulir->metode === 'transfer' && $formulir->bukti)
                    <div>
                        <p class="font-medium">Bukti Transfer:</p>
                        <a href="{{ asset('storage/' . $formulir->bukti) }}" target="_blank"
                            class="text-blue-600 hover:underline">Lihat Gambar</a>
                    </div>
                @endif
            </div>

            {{-- Daftar Wajib Pajak --}}
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Daftar Wajib Pajak</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Tahun</th>
                                <th class="px-4 py-2">NOP</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Pajak Terhutang</th>
                                <th class="px-4 py-2">Dharma Tirta</th>
                                <th class="px-4 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPbb = 0;
                                $totalDharma = 0;
                                $grandTotal = 0;
                                $no = 1;
                            @endphp

                            @foreach ($pbb as $item)
                                @php
                                    $jumlah = $item->pajak_terhutang + $item->dharma_tirta;
                                    $totalPbb += $item->pajak_terhutang;
                                    $totalDharma += $item->dharma_tirta;
                                    $grandTotal += $jumlah;
                                @endphp
                                <tr class="bg-white border-b">
                                    <td class="px-4 py-2">{{ $no++ }}</td>
                                    <td class="px-4 py-2">{{ $item->tahun }}</td>
                                    <td class="px-4 py-2">{{ $item->nop }}</td>
                                    <td class="px-4 py-2">{{ $item->wajibPajak->nama_wp ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ number_format($item->pajak_terhutang, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2">{{ number_format($item->dharma_tirta, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2">Rp {{ number_format($jumlah, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            <tr class="bg-gray-50 border-b font-bold">
                                <td colspan="4" class="px-4 py-2 text-right">JUMLAH</td>
                                <td class="px-4 py-2">{{ number_format($totalPbb, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ number_format($totalDharma, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('pembayaran.cetak', $formulir->id) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    Cetak Kwitansi
                </a>
                <a href="{{ route('pembayaran.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    Kembali
                </a>
            </div>
        </div>
    </div>
@endsection
