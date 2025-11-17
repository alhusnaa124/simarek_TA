@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <a href="{{ route('setoran') }}" class="inline-flex items-center text-gray-700 hover:text-green-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('setoran.update', $setoran->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal-setoran" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Setoran</label>
                        <input type="date" id="tanggal-setoran" name="tanggal" value="{{ $setoran->tanggal_setor }}"
                            class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            disabled>
                    </div>

                    <div>
                        <label for="petugas" class="block text-sm font-medium text-gray-700 mb-1">Petugas</label>
                        <input type="text" id="petugas" name="petugas" value="{{ $setoran->petugas->nama ?? '-' }}"
                            class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3" disabled>
                    </div>

                    <div>
                        <label for="jumlah-setoran" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Setoran
                            (Rp)</label>
                        <input type="text" id="jumlah-setoran" name="jumlah_setor"
                            value="RP {{ number_format($setoran->jumlah_setor, 0, ',' , '.') }}"
                            class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            disabled>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="flex space-x-4">
                            <button type="button" id="btnDiterima"
                                class="py-2 px-4 rounded-md text-sm bg-gray-100 text-gray-600 border border-gray-300 hover:bg-green-100 focus:outline-none"
                                data-status="Di terima">Diterima</button>
                            <button type="button" id="btnDitolak"
                                class="py-2 px-4 rounded-md text-sm bg-gray-100 text-gray-600 border border-gray-300 hover:bg-red-100 focus:outline-none"
                                data-status="Di Tolak">Ditolak</button>
                        </div>
                        <input type="hidden" name="status" id="statusInput" value="{{ $setoran->status ?? 'Menunggu' }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="catatan" name="catatan" rows="3"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Tambahkan catatan untuk setoran ini...">{{ $setoran->catatan }}</textarea>
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('setoran') }}"
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
        document.addEventListener('DOMContentLoaded', function() {
            const btnDiterima = document.getElementById('btnDiterima');
            const btnDitolak = document.getElementById('btnDitolak');
            const statusInput = document.getElementById('statusInput');

            // Inisialisasi tombol status aktif
            updateButtonStatus(statusInput.value);

            btnDiterima.addEventListener('click', function() {
                updateButtonStatus('Di terima');
                statusInput.value = 'Di terima';
            });

            btnDitolak.addEventListener('click', function() {
                updateButtonStatus('Di tolak');
                statusInput.value = 'Di tolak';
            });

            function updateButtonStatus(status) {
                btnDiterima.classList.remove('bg-green-100', 'text-green-700', 'border-green-500', 'bg-white',
                    'text-gray-500', 'border-gray-300');
                btnDitolak.classList.remove('bg-red-100', 'text-red-700', 'border-red-500', 'bg-white',
                    'text-gray-500', 'border-gray-300');

                btnDiterima.classList.add('bg-white', 'text-gray-500', 'border-gray-300');
                btnDitolak.classList.add('bg-white', 'text-gray-500', 'border-gray-300');

                if (status === 'Di terima') {
                    btnDiterima.classList.remove('bg-white', 'text-gray-500', 'border-gray-300');
                    btnDiterima.classList.add('bg-green-100', 'text-green-700', 'border-green-500');
                } else if (status === 'Di tolak') {
                    btnDitolak.classList.remove('bg-white', 'text-gray-500', 'border-gray-300');
                    btnDitolak.classList.add('bg-red-100', 'text-red-700', 'border-red-500');
                }
            }
        });
    </script>
@endpush
