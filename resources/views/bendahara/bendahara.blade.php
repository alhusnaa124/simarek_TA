@extends('layout')

@section('content')
    <style>
        div.dataTables_filter {
            margin-bottom: 1rem;
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
                <h1 class="text-2xl font-bold mb-4">DATA BENDAHARA</h1>

                <div class="flex justify-end mb-4">
                    <a href="{{ route('bendahara.create') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700"
                        onclick="openTambahBendahara()">
                        + TAMBAH BENDAHARA
                    </a>
                </div>



                <div class="overflow-x-auto">
                    <table id="dataTable" class="min-w-full text-sm text-left border border-green-300 rounded-lg">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-6">No</th>
                                <th class="py-3 px-6">Nama</th>
                                <th class="py-3 px-6">Alamat</th>
                                <th class="py-3 px-6">Email</th>
                                <th class="py-3 px-6">Password</th>
                                <th class="py-3 px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bendahara as $index => $b)
                                <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                                    <td class="py-3 px-6">{{ $b->nama }}</td>
                                    <td class="py-3 px-6">{{ $b->alamat }}</td>
                                    <td class="py-3 px-6">{{ $b->email }}</td>
                                    <td class="py-3 px-6">{{ str_repeat('*', min(strlen($b->password), 8)) }}</td>
                                    <td class="py-3 px-6">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('bendahara.edit', $b->id) }}"
                                                class="text-yellow-500 hover:text-yellow-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button class="text-red-500 hover:text-red-600"
                                                onclick="openModal({{ $b->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @include('bendahara.hapus')
                                        </div>
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
                ordering: false,
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
