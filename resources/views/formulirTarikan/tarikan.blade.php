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
<div class="bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-4">DATA FORMULIR TARIKAN</h1>

    <div class="overflow-x-auto">
        <table id="dataTable" class="min-w-full text-sm text-left border rounded-lg">
            <thead class="bg-green-500 text-gray-800 uppercase">
                <tr>
                    <th class="py-3 px-6">No</th>
                    <th class="py-3 px-6">ID</th>
                    <th class="py-3 px-6">Nama Kelompok</th>
                    <th class="py-3 px-6">Jumlah PBB</th>
                    <th class="py-3 px-6">Total</th>
                    <th class="py-3 px-6">Status</th>
                    <th class="py-3 px-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @php
                    $kelompokTarikan = $kelompokList
                        ->filter(function ($item) {
                            return $item->formulirTarikan !== null;
                        })
                        ->values();
                @endphp

                @foreach ($formulirs as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-6">{{ $index + 1 }}</td>
                        <td class="py-3 px-6">{{ $item->id }}</td>
                        <td class="py-3 px-6">{{ $item->kelompok->nama_kelompok }}</td>
                        <td class="py-3 px-6">
                            {{ $item->kelompok->wajibPajak->flatMap->pbb->where('tahun', $item->tahun)->count() }}
                        </td>
                        <td class="py-3 px-6">Rp. {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="py-3 px-6">{{ $item->status }}</td>
                        <td class="py-3 px-6">
                            <a href="{{ route('formulir.export', ['id' => $item->id]) }}" target="_blank"
                                class="text-blue-600 hover:text-blue-800 flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M6 2a1 1 0 00-1 1v2h10V3a1 1 0 00-1-1H6zM5 6v2H4a2 2 0 00-2 2v5a2 2 0 002 2h2v2a1 1 0 001 1h6a1 1 0 001-1v-2h2a2 2 0 002-2v-5a2 2 0 00-2-2h-1V6H5zm2 9v2h6v-2H7z" />
                                </svg>
                                <span>PDF</span>
                            </a>
                        </td>
                    </tr>
                @endforeach


            </tbody>

        </table>
    </div>
</div>


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
