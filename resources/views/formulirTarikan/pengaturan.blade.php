@extends('layout')

@section('content')
    {{-- Alert Success --}}
    @if (session('success'))
        <div id="alert-success"
            class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Success</span>
            <div>
                {{ session('success') }}
            </div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8"
                data-dismiss-target="#alert-success" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l6 6m0-6L4 10"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="container mx-auto mt-5">
        <div class="bg-white p-6 rounded-xl shadow-lg">

            <h1 class="text-2xl font-bold text-gray-800 mb-4">Pengaturan Formulir</h1>

            <form action="{{ route('config.formulir.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Jadwal Pembayaran --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Jadwal Pembayaran
                    </label>
                    <input type="date" name="jadwal" value="{{ $config['jadwal_pembayaran'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        required>
                </div>
                {{-- nama Kepala desa --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kepala Desa
                    </label>
                    <input type="text" name="nama_kepalaDesa" value="{{ $config['nama_kepalaDesa'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        required>
                </div>

                {{-- Upload TTD --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        Upload Tanda Tangan Kepala Desa
                    </label>
                    <input type="file" name="ttd" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">

                    {{-- Preview TTD --}}
                    <div class="mt-3">
                        @if (!empty($config['ttd_kepala_desa']) && file_exists(public_path('ttd/' . $config['ttd_kepala_desa'])))
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <img src="{{ asset('ttd/' . $config['ttd_kepala_desa']) }}" alt="Tanda Tangan"
                                    class="w-24 h-16 object-contain border border-gray-200 rounded">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Tanda tangan saat ini</p>
                                    <p class="text-xs text-gray-500">Upload file baru untuk mengganti</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 p-3 bg-yellow-50 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <p class="text-sm text-yellow-700">Tanda tangan belum diatur</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('formulir') }}"
                        class="inline-flex items-center px-4 py-2  bg-blue-600 text-sm font-medium  rounded-lg hover:bg-blue-800 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                        Kembali
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
