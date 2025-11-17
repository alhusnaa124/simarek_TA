@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <a href="{{ route('kelompok.index') }}" class="inline-flex items-center text-gray-700 hover:text-green-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Kelompok
                </a>
            </div>

            <form action="{{ route('kelompok.update', $kelompok->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelompok</label>
                        <input type="text" id="nama" name="nama_kelompok"
                            value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('nama_kelompok')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('kelompok.index') }}"
                        class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-200 w-40 text-center">
                        BATAL
                    </a>
                    <button type="submit"
                        class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 w-40">
                        SIMPAN
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
