@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <div class="mb-6 flex items-center">
        <h1 class="text-2xl font-bold">DISTRIBUSI PBB → Edit Petugas</h1>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4">
            <a href="{{ route('distribusi') }}" class="inline-flex items-center text-gray-700 hover:text-green-600">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Distribusi
            </a>
        </div>
        <form method="POST" action="{{ route('distribusi.update', $distribusi->no_wp) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nop" class="block text-sm font-medium text-gray-700 mb-1">NOP</label>
                    <input type="text" id="nop" name="nop" value="{{ $distribusi->nop }}" class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3" readonly>
                </div>
                <div>
                    <label for="nama_wp" class="block text-sm font-medium text-gray-700 mb-1">Nama Wajib Pajak</label>
                    <input type="text" id="nama_wp" name="nama_wp" value="{{ $distribusi->wajibPajak->nama_wp }}" class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3" readonly>
                </div>
                <div class="md:col-span-2">
                    <label for="id_petugas" class="block text-sm font-medium text-gray-700 mb-1">Petugas</label>
                    <select id="id_petugas" name="id_petugas" class="w-full border border-gray-300 rounded-md py-2 px-3">
                        <option value="">Pilih Petugas</option>
                        @foreach($petugas as $p)
                            <option value="{{ $p->id }}" {{ ($distribusi->wajibPajak->id_petugas ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-between mt-6">
                <a href="{{ route('distribusi') }}" class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 w-40 text-center">BATAL</a>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 w-40">SIMPAN</button>
            </div>
        </form>
    </div>
</div>
@endsection
