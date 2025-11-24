@extends('layout')

@section('content')
    <style>
        /* Tambah jarak dasar */
        div.dataTables_filter {
            margin-bottom: 1rem;
        }

        /* Wrapper jarak kiri kanan */
        .dataTables_wrapper {
            padding-left: 12px;
            padding-right: 12px;
        }

        @media screen and (max-width: 768px) {

            /* Semua elemen DataTables rata kiri */
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: left !important;
            }

            /* Bungkus filter: rapih, 1 baris, rata kiri */
            div.dataTables_filter {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: flex-start !important;
                gap: 6px !important;
                width: 100% !important;
            }

            /* Input search kecil */
            div.dataTables_filter input {
                width: 140px !important;
            }

            /* Dropdown kecil */
            .dataTables_length select {
                width: 70px !important;
            }

            /* Jarak kiri-kanan supaya tidak mepet */
            .dataTables_length,
            .dataTables_filter {
                margin-left: 4px !important;
                margin-right: 4px !important;
            }
        }
    </style>

    {{-- alert --}}
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
            <div>{{ session('success') }}</div>
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
            <h1 class="text-2xl font-bold mb-4">PEMBAYARAN PBB</h1>

            {{-- Tab Menu --}}
            <div class="mb-6 border-b border-gray-200">
                <nav class="flex space-x-4">
                    <a href="{{ route('pembayaran.index', ['status' => 'belum']) }}"
                        class="px-4 py-2 font-medium {{ request('status') != 'sudah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Belum Dibayar
                    </a>
                    <a href="{{ route('pembayaran.index', ['status' => 'sudah']) }}"
                        class="px-4 py-2 font-medium {{ request('status') == 'sudah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Sudah Dibayar
                    </a>
                </nav>
            </div>

            <h3 class="mb-4 text-gray-500">
                Daftar Formulir yang {{ request('status') == 'sudah' ? 'Sudah Dibayar' : 'Belum Dibayar' }}
            </h3>

            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-6">No</th>
                            <th class="py-3 px-6">ID Formulir</th>
                            <th class="py-3 px-6">Nama Kelompok</th>
                            <th class="py-3 px-6">Tahun</th>
                            <th class="py-3 px-6">Total PBB</th>
                            <th class="py-3 px-6">Keterangan</th>
                            <th class="py-3 px-6">Bukti</th>
                            <th class="py-3 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $processedKelompok = [];
                        @endphp

                        @foreach ($processedFormulirs as $index => $formulir)
                            @php
                                $kelompokFormulirs = $processedFormulirs->filter(
                                    fn($f) => $f->kelompok->id == $formulir->kelompok->id,
                                );

                                // Cek apakah semua formulir dalam kelompok belum lunas
                                $semuaBelumLunas = $kelompokFormulirs->every(fn($f) => $f->status === 'belum lunas');

                                // Hitung total gabungan jika semua belum lunas
                                $totalGabungan = $semuaBelumLunas ? $kelompokFormulirs->sum('total') : $formulir->total;

                                // Untuk menampilkan tahun yang digabung
                                $tahunGabungan = $semuaBelumLunas
                                    ? $kelompokFormulirs->pluck('tahun')->sort()->join(', ')
                                    : $formulir->tahun;

                                // Keterangan status
                                $keterangan = $semuaBelumLunas
                                    ? 'Gabungan (' . $kelompokFormulirs->count() . ' tahun)'
                                    : 'Individual';

                                // Skip jika kelompok sudah diproses dan dalam mode gabungan
                                if ($semuaBelumLunas && in_array($formulir->id_kelompok, $processedKelompok)) {
                                    continue;
                                }

                                // Tandai kelompok sebagai sudah diproses
                                if ($semuaBelumLunas) {
                                    $processedKelompok[] = $formulir->id_kelompok;
                                }
                            @endphp

                            <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                <td class="py-3 px-6">{{ $loop->iteration }}</td>
                                <td class="py-3 px-6">
                                    @if ($formulir->is_gabungan)
                                        <span class="text-blue-600 font-medium">{{ $formulir->formulir_ids }}</span>
                                    @else
                                        {{ $formulir->id }}
                                    @endif
                                </td>
                                <td class="py-3 px-6">{{ $formulir->kelompok->nama_kelompok }}</td>
                                <td class="py-3 px-6">
                                    <span class="{{ $formulir->is_gabungan ? 'text-blue-600 font-medium' : '' }}">
                                        {{ $formulir->tahun_gabungan }}
                                    </span>
                                </td>
                                <td class="py-3 px-6">
                                    <span class="{{ $formulir->is_gabungan ? 'text-blue-600 font-bold' : '' }}">
                                        Rp {{ number_format($formulir->total_gabungan, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3 px-6">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
            {{ $formulir->is_gabungan ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $formulir->is_gabungan ? 'Gabungan (' . $formulir->jumlah_formulir . ' tahun)' : 'Individual' }}
                                    </span>
                                </td>
                                <td class="py-3 px-6">
                                    @if ($formulir->bukti)
                                        <a href="{{ asset('storage/' . $formulir->bukti) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $formulir->bukti) }}" alt="Bukti"
                                                class="w-16 h-16 object-cover rounded hover:scale-110 transition" />
                                        </a>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6">
                                    @if (request('status') == 'sudah')
                                        @if ($formulir->status === 'lunas')
                                            <div class="flex gap-3 items-center">
                                                <a href="{{ route('pembayaran.detail', $formulir->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                                    title="Lihat Detail">
                                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-width="2"
                                                            d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                                        <path stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="openModal('{{ $formulir->id }}')"
                                                    class="text-red-500 hover:text-red-600" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                @include('pembayaran.hapus')
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex flex-col gap-1">
                                            <a href="{{ route('pembayaran.create', ['formulir_id' => $formulir->id]) }}"
                                                class="inline-flex items-center text-sm px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Bayar
                                            </a>
                                            @if ($formulir->is_gabungan)
                                                <small class="text-blue-600 text-xs">Pembayaran Gabungan</small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                paging: true,
                ordering: false,
                info: true,
                lengthMenu: [
                    [10, 50, 100, 1000],
                    [10, 50, 100, 1000]
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari total _MAX_ data)",
                    paginate: {
                        previous: "←",
                        next: "→"
                    }
                },
                initComplete: function() {
                    $('div.dataTables_length select').addClass(
                        'border border-gray-300 rounded px-2 py-1 text-sm shadow-sm w-16 focus:outline-none focus:ring-2 focus:ring-green-500'
                    );
                }
            });
        });
    </script>
@endpush
