@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <a href="{{ route('bendahara.index') }}" class="inline-flex items-center text-gray-700 hover:text-green-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            <h2 class="text-xl font-semibold mb-4 text-gray-700">Tambah Bendahara</h2>

            <form method="POST" action="{{ route('bendahara.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama bendahara"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                        @error('nama')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan email"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                        @error('email')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                </div>
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    {{-- <textarea id="alamat" name="alamat" placeholder="Masukkan alamat"
                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        rows="3" required>{{ old('alamat') }}</textarea> --}}
                    <textarea id="alamat" name="alamat" placeholder="Masukkan alamat"
                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        rows="3" required></textarea>
                    @error('alamat')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror


                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('bendahara.index') }}"
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

@push('scripts')
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: true
            });
        @endif
    </script>
@endpush
