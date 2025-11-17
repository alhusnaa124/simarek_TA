@extends('layout')

@section('content')
    <style>
        div.dataTables_filter {
            margin-bottom: 1rem;
        }
    </style>
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

    {{-- Alert Error --}}
    @if (session('error'))
        <div id="alert-error"
            class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50"
            role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M18 10A8 8 0 11.001 10 8 8 0 0118 10zM9 4a1 1 0 012 0v4a1 1 0 01-2 0V4zm1 8a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Error</span>
            <div>
                {{ session('error') }}
            </div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-800 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-100 inline-flex items-center justify-center h-8 w-8"
                data-dismiss-target="#alert-error" aria-label="Close">
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
            <h1 class="text-2xl font-bold mb-4">DATA WAJIB PAJAK</h1>

            @if (auth()->user()->role === 'Admin')
                <div class="flex justify-end mb-4">
                    <button data-modal-target="upload-modal" data-modal-toggle="upload-modal"
                        class="block text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-4 py-2 text-center"
                        type="button">
                        + Upload File
                    </button>
                </div>
                @include('wajib_pajak.upload_file')
            @endif

            {{-- filter tahun --}}
            <div class="flex gap-4 mb-4">
                <select id="filter-tahun"
                    class="border border-gray-300 rounded px-3 py-2 w-40 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Tahun</option>
                    @foreach ($pbbList->pluck('tahun')->unique()->sort()->reverse() as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <select id="filter-status"
                    class="border border-gray-300 rounded px-3 py-2 w-40 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="Lunas">Lunas</option>
                    <option value="Belum">Belum</option>
                </select>
            </div>


            <div class="overflow-x-auto">
                <table id="dataTable" class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-6">No</th>
                            <th class="py-3 px-6">NOP</th>
                            <th class="py-3 px-6">Nama WP</th>
                            <th class="py-3 px-6">Alamat WP</th>
                            <th class="py-3 px-6">Alamat OP</th>
                            <th class="py-3 px-6">Luas Tanah</th>
                            <th class="py-3 px-6">Luas Bangunan</th>
                            <th class="py-3 px-6">Pajak Terhutang</th>
                            <th class="py-3 px-6">Dharma Tirta</th>
                            <th class="py-3 px-6">Jenis</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6">Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pbbList as $index => $pbb)
                            <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                <td class="py-3 px-6">{{ $loop->iteration }}</td>
                                <td class="py-3 px-6">{{ $pbb->nop }}</td>
                                <td class="py-3 px-6">{{ $pbb->wajibPajak->nama_wp }}</td>
                                <td class="py-3 px-6">{{ $pbb->wajibPajak->alamat_wp }}</td>
                                <td class="py-3 px-6">{{ $pbb->alamat_op }}</td>
                                <td class="py-3 px-6">{{ $pbb->luas_tnh }} m²</td>
                                <td class="py-3 px-6">{{ $pbb->luas_bgn }} m²</td>
                                <td class="py-3 px-6">{{ number_format($pbb->pajak_terhutang, 0, ',', '.') }}</td>
                                <td class="py-3 px-6">{{ number_format($pbb->dharma_tirta, 0, ',', '.') }}</td>
                                <td class="py-3 px-6">{{ $pbb->jenis }}</td>
                                <td
                                    class="{{ $pbb->isLunas() ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ $pbb->isLunas() ? 'Lunas' : 'Belum' }}
                                </td>


                                <td class="py-3 px-6">{{ $pbb->tahun }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
            var table = $('#dataTable').DataTable({
                paging: true,
                ordering: true,
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
                    let filterTahun = $('#filter-tahun').detach();
                    let filterStatus = $('#filter-status').detach();
                    $('div.dataTables_filter').append(filterTahun).append(filterStatus);
                    $('div.dataTables_filter').addClass(
                        'flex gap-4 items-center justify-end'); // styling flex
                }
            });

            // ✅ Filtering berdasarkan dropdown tahun (gunakan instance yg sudah ada)
            $('#filter-tahun').on('change', function() {
                var selectedYear = $(this).val();
                if (selectedYear) {
                    table.column(11).search('^' + selectedYear + '$', true, false).draw();
                } else {
                    table.column(11).search('').draw();
                }
            });
            // ✅ Filtering berdasarkan status lunas/belum
            $('#filter-status').on('change', function() {
                var selectedStatus = $(this).val();
                if (selectedStatus) {
                    table.column(10).search('^' + selectedStatus + '$', true, false).draw();
                } else {
                    table.column(10).search('').draw();
                }
            });

        });
    </script>
@endpush
