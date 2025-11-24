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
            <div class="container mx-auto mt-8">
                <h1 class="text-2xl font-bold mb-4">DATA KELOMPOK</h1>


                <div class="overflow-x-auto">
                    <table id="dataTable" class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">Nama Kelompok</th>
                                <th class="py-3 px-6">Alamat</th>
                                @foreach ($tahunList as $tahun)
                                    <th class="py-3 px-6">{{ $tahun }}</th>
                                @endforeach
                                <th class="py-3 px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kelompok as $index => $klmpk)
                                <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                    <td class="py-3 px-6">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-6">{{ $klmpk->nama_kelompok ?? '-' }}</td>
                                    <td class="py-3 px-6">{{ $klmpk->alamat_singkat ?? '-' }}</td>

                                    @foreach ($tahunList as $tahun)
                                        <td class="py-3 px-6">
                                            {{ $klmpk->tahun_pbb[$tahun] ?? 0 }} PBB
                                        </td>
                                    @endforeach

                                    <td class="py-3 px-6 flex space-x-2">
                                        <a href="{{ route('kelompok.edit', $klmpk->id) }}"
                                            class="text-amber-500 hover:text-amber-600" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button type="button" onclick="openModal({{ $klmpk->id }})"
                                            class="text-red-500 hover:text-red-600" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        @include('kelompok.hapus')
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                paging: true,
                ordering: true,
                info: true, // disamakan agar muncul info total data dan halaman
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
