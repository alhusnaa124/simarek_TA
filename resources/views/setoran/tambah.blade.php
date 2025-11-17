@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="mb-6 flex flex-col ">
            <h1 class="text-3xl font-bold text-gray-900">Tambah Setoran</h1>
            <p class="mt-2 text-gray-600">Pilih formulir yang sudah lunas untuk disetorkan</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">


            <form id="setoranForm" action="{{ route('setoran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tanggal Setoran -->
                <div class="mb-4">
                    <label for="tanggal_setor" class="block mb-2 text-sm font-medium text-gray-900">
                        Tanggal Setoran
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="date" id="tanggal_setor" name="tanggal_setor" value="{{ date('Y-m-d') }}" readonly
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
                    </div>
                </div>

                <!-- Dropdown Formulir -->
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">
                        Pilih Formulir yang Akan Disetorkan
                    </label>

                    <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch"
                        data-dropdown-placement="bottom-start"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center"
                        type="button">
                        <span id="selectedCount">Pilih Formulir</span>
                        <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-full max-w-md border">
                        <div class="p-3">
                            <label for="input-group-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="input-group-search"
                                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Cari formulir...">
                            </div>
                        </div>
                        <ul class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700" id="formulirList">
                            @forelse ($formulir as $formulir)
                                <li class="formulir-item">
                                    <div class="flex items-center p-2 rounded hover:bg-gray-100">
                                        @if ($formulir->gabungan ?? false)
                                            {{-- Checkbox khusus untuk gabungan --}}
                                            <input type="checkbox" id="checkbox-item-{{ $formulir->id }}"
                                                class="formulir-checkbox gabungan-checkbox"
                                                data-total="{{ $formulir->total }}"
                                                data-ids="{{ implode(',', $formulir->ids_digabung) }}"
                                                value="{{ $formulir->id }}">

                                            <label for="checkbox-item-{{ $formulir->id }}"
                                                class="w-full ml-2 text-sm font-medium text-gray-900 cursor-pointer">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <div class="font-semibold">
                                                            #Gabungan - {{ $formulir->kelompok->nama_kelompok }}
                                                            ({{ implode(', ', $formulir->tahun_digabung) }})
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Status: {{ $formulir->status }} |
                                                            Tanggal:
                                                            {{ \Carbon\Carbon::parse($formulir->tgl_bayar)->format('d/m/Y') }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </label>
                                        @else
                                            {{-- Formulir biasa --}}
                                            <input id="checkbox-item-{{ $formulir->id }}" type="checkbox"
                                                name="formulir_id[]" value="{{ $formulir->id }}"
                                                data-total="{{ $formulir->total }}"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 formulir-checkbox">
                                            <label for="checkbox-item-{{ $formulir->id }}"
                                                class="w-full ml-2 text-sm font-medium text-gray-900 cursor-pointer">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <div class="font-semibold">
                                                            #{{ $formulir->id }} -
                                                            {{ $formulir->kelompok->nama_kelompok }}
                                                            ({{ $formulir->tahun }})
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Status: {{ $formulir->status }} |
                                                            Tanggal:
                                                            {{ \Carbon\Carbon::parse($formulir->tgl_bayar)->format('d/m/Y') }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </label>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="p-4 text-center text-gray-500">
                                    <div class="text-sm">Tidak ada formulir yang lunas</div>
                                    <div class="text-xs mt-1">Silakan selesaikan pembayaran formulir terlebih dahulu</div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Selected Items Display -->
                <div id="selectedItems" class="mb-6 hidden">
                    <label class="block mb-2 text-sm font-medium text-gray-900">
                        Formulir Terpilih
                    </label>
                    <div id="selectedItemsList" class="space-y-2">
                        <!-- Selected items will be displayed here -->
                    </div>
                </div>

                <!-- Total Setoran -->
                <div class="mb-6">
                    <label for="jumlah_setor" class="block mb-2 text-sm font-medium text-gray-900">
                        Total Setoran
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">Rp</span>
                        </div>
                        <input type="text" id="jumlah_setor_display" readonly
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                            value="0">
                        <input type="hidden" id="jumlah_setor" name="jumlah_setor" value="0">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Total akan dihitung otomatis berdasarkan formulir yang dipilih
                    </p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-900">Upload Bukti Setoran</label>
                    <input type="file" name="bukti" accept="image/*"
                        class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-sm text-gray-500">Hanya file JPG, JPEG, PNG</p>
                </div>


                <!-- Tombol -->
                <div class="flex justify-between">
                    <button type="button" onclick="window.history.back()"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">

                        Batal
                    </button>
                    <button type="submit" id="submitBtn" disabled
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        Simpan Setoran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Checkbox gabungan
            document.querySelectorAll('.gabungan-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const ids = this.dataset.ids.split(',');
                    const container = this.closest('.formulir-item');

                    if (this.checked) {
                        ids.forEach(id => {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'formulir_id[]';
                            hidden.value = id;
                            hidden.classList.add('auto-injected-id');
                            container.appendChild(hidden);
                        });
                    } else {
                        container.querySelectorAll('.auto-injected-id').forEach(el => el.remove());
                    }

                    updateTotal(); // Panggil ulang agar total disesuaikan
                });
            });

            // Set tanggal hari ini
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal_setor').value = today;

            const checkboxes = document.querySelectorAll('.formulir-checkbox');
            const totalDisplay = document.getElementById('jumlah_setor_display');
            const totalHidden = document.getElementById('jumlah_setor');
            const selectedCount = document.getElementById('selectedCount');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');
            const selectedItems = document.getElementById('selectedItems');
            const selectedItemsList = document.getElementById('selectedItemsList');
            const searchInput = document.getElementById('input-group-search');
            const formulirItems = document.querySelectorAll('.formulir-item');

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                formulirItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            function updateTotal() {
                let total = 0;
                let count = 0;
                const selectedData = [];

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const amount = parseInt(checkbox.dataset.total);
                        total += amount;
                        count++;

                        const label = checkbox.nextElementSibling;
                        const nameElement = label.querySelector('.font-semibold');
                        const amountElement = label.querySelector('.font-bold');

                        selectedData.push({
                            id: checkbox.value,
                            name: nameElement?.textContent ?? '',
                            amount: parseInt(checkbox.dataset.total).toLocaleString('id-ID')
                        });

                    }
                });

                totalDisplay.value = total.toLocaleString('id-ID');
                totalHidden.value = total;

                if (count > 0) {
                    selectedCount.textContent = `${count} Formulir Dipilih`;
                    submitBtn.disabled = false;
                    alertContainer?.classList?.add('hidden');
                    selectedItems.classList.remove('hidden');
                    selectedItemsList.innerHTML = '';

                    selectedData.forEach(item => {
                        const itemDiv = document.createElement('div');
                        itemDiv.className =
                            'flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200';
                        itemDiv.innerHTML = `
                            <div class="flex justify-between w-full">
                                <div class="font-medium text-blue-900">${item.name}</div>
                                <div class="font-bold text-blue-700">Rp ${item.amount}</div>
                            </div>`;
                        selectedItemsList.appendChild(itemDiv);
                    });

                } else {
                    selectedCount.textContent = 'Pilih Formulir';
                    submitBtn.disabled = true;
                    selectedItems.classList.add('hidden');
                }
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotal);
            });

            updateTotal();
        });
    </script>
@endpush
