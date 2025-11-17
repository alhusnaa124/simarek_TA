@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4">
                <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center text-gray-700">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Tambah Pembayaran
                </a>
            </div>

            @php
                // Ambil semua formulir dalam kelompok yang sama
                $kelompokFormulirs = \App\Models\FormulirTarikan::where('id_kelompok', $formulir->id_kelompok)->get();
                
                // Cek apakah semua formulir dalam kelompok belum lunas
                $semuaBelumLunas = $kelompokFormulirs->every(fn($f) => $f->status === 'belum lunas');
                
                // Hitung total gabungan jika semua belum lunas
                $totalGabungan = $semuaBelumLunas ? $kelompokFormulirs->sum('total') : $formulir->total;
                
                // Untuk menampilkan tahun yang digabung
                $tahunGabungan = $semuaBelumLunas ? $kelompokFormulirs->pluck('tahun')->sort()->join(', ') : $formulir->tahun;
                
                // Untuk menampilkan ID formulir yang digabung
                $idGabungan = $semuaBelumLunas ? $kelompokFormulirs->pluck('id')->join(', ') : $formulir->id;
            @endphp

            @if ($semuaBelumLunas && $kelompokFormulirs->count() > 1)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-blue-800">Pembayaran Gabungan</h3>
                    </div>
                    <p class="text-blue-700">
                        Sistem mendeteksi bahwa semua formulir dalam kelompok ini belum lunas. 
                        Pembayaran akan otomatis menggabungkan <strong>{{ $kelompokFormulirs->count() }} formulir</strong> 
                        untuk tahun <strong>{{ $tahunGabungan }}</strong>.
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('pembayaran.store', $formulir->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="font-semibold">ID Formulir</label>
                        <input readonly value="{{ $idGabungan }}" 
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 
                                @if($semuaBelumLunas && $kelompokFormulirs->count() > 1) text-blue-600 font-medium @endif" />
                        @if ($semuaBelumLunas && $kelompokFormulirs->count() > 1)
                            <small class="text-blue-600 text-xs mt-1">Multiple ID (Pembayaran Gabungan)</small>
                        @endif
                    </div>
                    
                    <div>
                        <label class="font-semibold">Nama Kelompok</label>
                        <input readonly value="{{ $formulir->kelompok->nama_kelompok }}" 
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50" />
                    </div>
                    
                    <div>
                        <label class="font-semibold">Tahun</label>
                        <input readonly value="{{ $tahunGabungan }}" 
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50
                                @if($semuaBelumLunas && $kelompokFormulirs->count() > 1) text-blue-600 font-medium @endif" />
                        @if ($semuaBelumLunas && $kelompokFormulirs->count() > 1)
                            <small class="text-blue-600 text-xs mt-1">Multiple Tahun (Pembayaran Gabungan)</small>
                        @endif
                    </div>
                    
                    <div>
                        <label class="font-semibold">Total</label>
                        <input readonly value="Rp {{ number_format($totalGabungan, 0, ',', '.') }}"
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50
                                @if($semuaBelumLunas && $kelompokFormulirs->count() > 1) text-blue-600 font-bold @endif" />
                        @if ($semuaBelumLunas && $kelompokFormulirs->count() > 1)
                            <small class="text-blue-600 text-xs mt-1">Total Gabungan ({{ $kelompokFormulirs->count() }} formulir)</small>
                        @endif
                    </div>
                    
                    <div>
                        <label class="font-semibold">Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" 
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" 
                            value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div>
                        <label class="font-semibold">Metode Pembayaran</label>
                        <select name="metode" 
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" 
                            id="metode-select" required>
                            <option value="tunai">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    
                    <div id="bukti-transfer" style="display: none;">
                        <label class="font-semibold">Bukti Transfer</label>
                        <input type="file" name="bukti" 
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" 
                            accept="image/*,application/pdf" />
                        <small class="text-gray-500 text-xs mt-1">Format: JPG, JPEG, PNG, PDF (Max: 2MB)</small>
                    </div>
                </div>

                @if ($semuaBelumLunas && $kelompokFormulirs->count() > 1)
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-semibold text-yellow-800 mb-2">Detail Formulir yang Akan Dibayar:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($kelompokFormulirs as $form)
                                <div class="bg-white p-3 rounded border">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium">ID: {{ $form->id }}</span>
                                        <span class="text-sm text-gray-600">Tahun: {{ $form->tahun }}</span>
                                    </div>
                                    <div class="text-sm text-gray-700 mt-1">
                                        Total: Rp {{ number_format($form->total, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-between mt-6">
                    <a href="{{ route('pembayaran.index') }}"
                        class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-200 w-40 text-center">
                        BATAL
                    </a>
                    <button id="submitBtn" type="submit"
                        class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 w-40">
                        SIMPAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const metodeSelect = document.getElementById("metode-select");
            const buktiTransfer = document.getElementById("bukti-transfer");
            const buktiInput = buktiTransfer.querySelector('input[name="bukti"]');
            const submitBtn = document.getElementById('submitBtn');

            function toggleBukti() {
                if (metodeSelect.value === "transfer") {
                    buktiTransfer.style.display = "block";
                    buktiInput.setAttribute('required', 'required');
                } else {
                    buktiTransfer.style.display = "none";
                    buktiInput.removeAttribute('required');
                }
            }

            metodeSelect.addEventListener("change", toggleBukti);

            // Cek nilai awal saat halaman dimuat
            toggleBukti();

            // Loading button on submit
            document.querySelector('form').addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Processing...';
            });
        });
    </script>
@endsection