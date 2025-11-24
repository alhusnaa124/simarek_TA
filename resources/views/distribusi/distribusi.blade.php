@extends('layout')
@section('content')
    <style>
        div.dataTables_filter {
            margin-bottom: 1rem;
        }

        @media screen and (max-width: 768px) {

            /* Hapus align DataTables */
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: left !important;
            }

            /* Posisi filter - kiri, rapi, satu baris */
            div.dataTables_filter {
                display: flex !important;
                flex-direction: row !important;
                justify-content: flex-start !important;
                align-items: center !important;
                gap: 6px !important;
                width: 100% !important;
            }

            /* Ukuran search kecil */
            div.dataTables_filter input {
                width: 120px !important;
            }

            /* Dropdown filters kecil */
            #filter-tahun,
            #filter-petugas,
            #filter-bagian {
                width: 130px !important;
            }

            .dataTables_wrapper {
                padding-left: 10px;
                padding-right: 10px;
            }

            /* Biar "Tampilkan" dan "Cari" tidak mepet */
            .dataTables_length,
            .dataTables_filter {
                margin-left: 5px !important;
                margin-right: 5px !important;
            }
        }
    </style>

    {{-- Alert Success --}}
    @if (session('success'))
        <div id="alert-success"
            class="flex items-start p-3 sm:p-4 mb-3 sm:mb-4 text-xs sm:text-sm text-green-800 border border-green-300 rounded-lg bg-green-50"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mt-0.5 me-2 sm:me-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Success</span>
            <div class="flex-1">{{ session('success') }}</div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8 flex-shrink-0"
                data-dismiss-target="#alert-success" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l6 6m0-6L4 10"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if (session('error'))
        <div id="alert-error"
            class="flex items-start p-3 sm:p-4 mb-3 sm:mb-4 text-xs sm:text-sm text-red-800 border border-red-300 rounded-lg bg-red-50"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mt-0.5 me-2 sm:me-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M18 10A8 8 0 11.001 10 8 8 0 0118 10zM9 4a1 1 0 012 0v4a1 1 0 01-2 0V4zm1 8a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Error</span>
            <div class="flex-1">{{ session('error') }}</div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-800 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-100 inline-flex items-center justify-center h-8 w-8 flex-shrink-0"
                data-dismiss-target="#alert-error" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l6 6m0-6L4 10"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="container mx-auto mt-3 sm:mt-5 px-4 sm:px-6">
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg">
            <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">DISTRIBUSI PBB</h1>

            {{-- Tab Menu --}}
            <div class="mb-4 sm:mb-6 border-b border-gray-200 overflow-x-auto">
                <nav class="flex space-x-2 sm:space-x-4 min-w-max">
                    <a href="{{ route('distribusi') }}"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium whitespace-nowrap {{ request('filter') == null ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Semua
                    </a>
                    <a href="{{ route('distribusi', ['filter' => 'sudah']) }}"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium whitespace-nowrap {{ request('filter') == 'sudah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Sudah Didistribusi
                    </a>
                    <a href="{{ route('distribusi', ['filter' => 'belum']) }}"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium whitespace-nowrap {{ request('filter') == 'belum' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Belum Didistribusi
                    </a>
                </nav>
            </div>

            {{-- filter tahun --}}
            <div class="mb-3 sm:mb-4">
                <select id="filter-tahun"
                    class="w-full sm:w-40 border border-gray-300 rounded px-3 py-2 text-xs sm:text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Tahun</option>
                    @foreach ($dataDistribusi->pluck('tahun')->unique()->sort()->reverse() as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Petugas (Admin only) --}}
            @if (auth()->user()->role === 'Admin')
                <div class="mb-3 sm:mb-4">
                    <select id="filter-petugas"
                        class="w-full sm:w-48 border border-gray-300 rounded px-3 py-2 text-xs sm:text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Petugas</option>
                        @foreach ($dataDistribusi->pluck('wajibPajak')->filter()->pluck('petugas')->filter()->unique('nama')->sortBy('nama') as $petugas)
                            <option value="{{ $petugas->nama }}">{{ $petugas->alamat }} - {{ $petugas->nama }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif (auth()->user()->role === 'Petugas')
                <div class="mb-3 sm:mb-4">
                    <select id="filter-bagian"
                        class="w-full sm:w-48 border border-gray-300 rounded px-3 py-2 text-xs sm:text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Semua Bagian</option>
                        @foreach ($dataDistribusi->pluck('wajibPajak')->filter()->pluck('bagian')->filter()->unique()->sort() as $bagian)
                            <option value="{{ $bagian }}">{{ $bagian }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden">
                        <table id="dataTable" class="min-w-full text-xs sm:text-sm text-left border rounded-lg">
                            <thead class="bg-green-700 text-white">
                                <tr>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">No</th>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">NOP</th>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">Nama WP</th>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">Alamat WP</th>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">Alamat OP</th>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">Luas Tanah (ubin)</th>
                                    <th class="py-2 px-2 sm:py-3 sm:px-6">Tahun</th>

                                    @if (auth()->user()->role === 'Admin')
                                        <th class="py-2 px-2 sm:py-3 sm:px-6">Petugas</th>
                                        <th class="py-2 px-2 sm:py-3 sm:px-6">Edit Petugas</th>
                                    @elseif (auth()->user()->role === 'Petugas')
                                        <th class="py-2 px-2 sm:py-3 sm:px-6">Kelompok</th>
                                        <th class="py-2 px-2 sm:py-3 sm:px-6">Bagian</th>
                                        <th class="py-2 px-2 sm:py-3 sm:px-6">Edit</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach ($dataDistribusi as $index => $item)
                                    <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $loop->iteration }}</td>
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->nop }}</td>
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->wajibPajak->nama_wp }}</td>
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->wajibPajak->alamat_wp }}</td>
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->alamat_op }}</td>
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->luas_tnh * 0.07 }}</td>
                                        <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->tahun }}</td>

                                        @if (auth()->user()->role === 'Admin')
                                            <td class="py-2 px-2 sm:py-3 sm:px-6">
                                                {{ $item->petugas_nama ?? ($item->wajibPajak->petugas->nama ?? '-') }}
                                            </td>
                                            <td class="py-2 px-2 sm:py-3 sm:px-6">
                                                <a href="{{ route('distribusi.edit', $item->wajibPajak->id) }}"
                                                    class="text-amber-500 hover:text-amber-700">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            </td>
                                        @elseif (auth()->user()->role === 'Petugas')
                                            <td class="py-2 px-2 sm:py-3 sm:px-6">
                                                {{ $item->wajibPajak->kelompok->nama_kelompok ?? '-' }}</td>
                                            <td class="py-2 px-2 sm:py-3 sm:px-6">{{ $item->wajibPajak->bagian ?? '-' }}
                                            </td>
                                            <td class="py-2 px-2 sm:py-3 sm:px-6">
                                                <a href="{{ route('pembayaran.distribusi.editKelompok', $item->wajibPajak->id) }}"
                                                    class="text-amber-500 hover:text-amber-700">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
            let table = $('#dataTable').DataTable({
                paging: true,
                ordering: false,
                info: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [
                    [5, 10, 50, 100, 1000],
                    [5, 10, 50, 100, 1000]
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
                        'border border-gray-300 rounded px-2 py-1 text-xs sm:text-sm shadow-sm w-16 focus:outline-none focus:ring-2 focus:ring-green-500'
                    );

                    @if (auth()->user()->role === 'Admin')
                        let filterPetugas = $('#filter-petugas').detach();
                        $('div.dataTables_filter').append(filterPetugas);
                        $('div.dataTables_filter').addClass(
                            'flex flex-col sm:flex-row gap-2 sm:gap-4 items-stretch sm:items-center justify-end'
                        );
                    @elseif (auth()->user()->role === 'Petugas')
                        let fillterBagian = $('#filter-bagian').detach();
                        $('div.dataTables_filter').append(fillterBagian);
                        $('div.dataTables_filter').addClass(
                            'flex flex-col sm:flex-row gap-2 sm:gap-4 items-stretch sm:items-center justify-end'
                        );
                    @endif

                    let filterTahun = $('#filter-tahun').detach();
                    $('div.dataTables_filter').append(filterTahun);
                    $('div.dataTables_filter').addClass(
                        'flex flex-col sm:flex-row gap-2 sm:gap-4 items-stretch sm:items-center justify-end'
                    );

                    $('div.dataTables_filter input').addClass('w-full sm:w-auto text-xs sm:text-sm');
                }
            });

            @if (auth()->user()->role === 'Admin')
                $('#filter-petugas').on('change', function() {
                    const selected = $(this).val();
                    table.column(7).search(selected).draw();
                });
            @elseif (auth()->user()->role === 'Petugas')
                $('#filter-bagian').on('change', function() {
                    const selected = $(this).val();
                    table.column(8).search(selected).draw();
                });
            @endif

            $('#filter-tahun').on('change', function() {
                var selectedYear = $(this).val();
                if (selectedYear) {
                    table.column(6).search('^' + selectedYear + '$', true, false).draw();
                } else {
                    table.column(6).search('').draw();
                }
            });
        });
    </script>
@endpush
