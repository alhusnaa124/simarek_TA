@extends('layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Setoran PBB</h2>

        {{-- Informasi Setoran --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
            <div>
                <p class="font-medium">ID Setoran:</p>
                <p class="text-gray-900">{{ $setoran->id }}</p>
            </div>
            <div>
                <p class="font-medium">Nama Verifikator:</p>
                <p class="text-gray-900">{{ $setoran->verifikator->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium">Nama Petugas:</p>
                <p class="text-gray-900">{{ $setoran->petugas->nama ?? '-' }}</p>
            </div>
        </div>

        {{-- Daftar Formulir dalam Setoran --}}
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Formulir dalam Setoran</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Tahun</th>
                            <th class="px-4 py-2">Nama Kelompok</th>
                            <th class="px-4 py-2">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach ($formulirList as $index => $formulir)
                            @php $grandTotal += $formulir->total; @endphp
                            <tr class="bg-white border-b">
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $formulir->tahun }}</td>
                                <td class="px-4 py-2">{{ $formulir->kelompok->nama_kelompok ?? '-' }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($formulir->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold bg-gray-50 border-t">
                            <td class="px-4 py-2 text-right" colspan="2">Total Setoran</td>
                            <td class="px-4 py-2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-end gap-4 mt-6">
            <a href="{{ route('setoran') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
