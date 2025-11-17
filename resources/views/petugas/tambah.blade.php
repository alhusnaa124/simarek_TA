@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <a href="{{ route('petugas.index') }}" class="inline-flex items-center text-gray-700 hover:text-green-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Data Petugas
                </a>
            </div>

            <form id="form-tambah-petugas" method="POST" action="{{ route('petugas.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama petugas"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('nama') }}">
                        @error('nama')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan email"
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            value="{{ old('email') }}">
                        @error('email')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" id="alamat" name="alamat" placeholder="Masukkan alamat"
                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        value="{{ old('alamat') }}">
                    @error('alamat')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="label_petugas" class="block text-sm font-medium text-gray-700 mb-1">Label Petugas</label>
                    <select name="label_petugas" id="label_petugas"
                        class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="" selected>- Pilih Label Petugas -</option>

                        <optgroup label="Belum Digunakan">
                            @foreach ($allLabels as $label)
                                @if (!in_array($label, $usedLabels))
                                    <option value="{{ $label }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </optgroup>

                        <optgroup label="Gantikan Petugas Lama">
                            @foreach ($usedLabels as $label)
                                <option value="{{ $label }}">{{ $label }} (Gantikan)</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('label_petugas')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('petugas.index') }}"
                        class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-200 w-40 text-center">BATAL</a>
                    <button type="submit" id="btn-submit"
                        class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 w-40">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const usedLabels = @json($usedLabels);

        document.getElementById('form-tambah-petugas').addEventListener('submit', function(e) {
            const selectedLabel = document.getElementById('label_petugas').value;

            if (usedLabels.includes(selectedLabel)) {
                e.preventDefault(); // tahan submit

                Swal.fire({
                    title: 'Gantikan Petugas Lama?',
                    text: `Label "${selectedLabel}" sudah digunakan. Petugas lama akan di-nonaktifkan dan diganti.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ganti',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-tambah-petugas').submit();
                    }
                });
            }
        });
    </script>
@endpush
