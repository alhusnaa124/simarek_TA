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
            <div>{{ session('success') }}</div>
            <button type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-800 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8"
                data-dismiss-target="#alert-success" aria-label="Close">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 14 14"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l6 6m0-6L4 10"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="container mx-auto mt-5">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h1 class="text-2xl font-bold mb-4">DATA SETORAN</h1>

            {{-- Tab Menu --}}
            <div class="mb-6 border-b border-gray-200">
                <nav class="flex space-x-4">
                    <a href="{{ route('setoran', ['status' => 'belum']) }}"
                        class="px-4 py-2 font-medium {{ request('status') != 'sudah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Belum DiVerifikasi
                    </a>
                    <a href="{{ route('setoran', ['status' => 'sudah']) }}"
                        class="px-4 py-2 font-medium {{ request('status') == 'sudah' ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-500 hover:text-green-600' }}">
                        Sudah DiVerifikasi
                    </a>
                </nav>
            </div>

            {{-- Tombol Tambah --}}
            @if (auth()->user()->role === 'Petugas' && (request('status') === 'belum' || request('status') === null))
                <div class="flex justify-end mb-4">
                    <a href="{{ route('setoran.tambah') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">
                        + Tambah Setoran
                    </a>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table id="setoranTable" class="min-w-full text-sm text-left border rounded-lg">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-6">NO</th>
                            <th class="py-3 px-6">Verifikator</th>
                            <th class="py-3 px-6">Tanggal</th>
                            <th class="py-3 px-6">Jumlah</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6">Catatan</th>
                            <th class="py-3 px-6">Bukti</th>
                            <th class="py-3 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($setoran as $index => $item)
                            <tr class="odd:bg-white even:bg-green-50 hover:bg-green-200">
                                <td class="py-3 px-6">{{ $index + 1 }}</td>
                                <td class="py-3 px-6">{{ $item->verifikator->nama ?? '-' }}</td>
                                <td class="py-3 px-6">{{ \Carbon\Carbon::parse($item->tanggal_setor)->format('d-m-Y') }}
                                </td>
                                <td class="py-3 px-6">Rp. {{ number_format($item->jumlah_setor, 0, ',', '.') }}</td>
                                <td class="py-3 px-6">
                                    @if ($item->status === 'Di terima')
                                        <span
                                            class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Diterima</span>
                                    @elseif ($item->status === 'Di Tolak')
                                        <span
                                            class="bg-orange-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Ditolak</span>
                                    @else
                                        <span
                                            class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium">Menunggu</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6">{{ $item->catatan ?? '-' }}</td>
                                <td class="py-3 px-6">
                                    @if ($item->bukti)
                                        <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $item->bukti) }}" alt="Bukti"
                                                class="w-16 h-16 object-cover rounded border">
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 flex items-center space-x-2">
                                    @if (auth()->user()->role === 'Bendahara')
                                        <a href="{{ route('setoran.edit', $item->id) }}"
                                            class="text-yellow-500 hover:text-yellow-600" title="Verifikasi Setoran">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endif

                                    <a href="{{ route('setoran.detail', $item->id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm" title="Lihat Detail">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-width="2"
                                                d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                            <path stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>

                                    @if ($item->status === 'Di terima')
                                        <a href="{{ route('setoran.cetak', $item->id) }}"
                                            class="text-green-600 hover:text-green-800" title="Cetak Kwitansi"
                                            target="_blank">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 16h8M8 12h8m-7-8h6a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                            </svg>
                                        </a>
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
            $('#setoranTable').DataTable({
                paging: true,
                ordering: false,
                info: false,
                columnDefs: [{
                    targets: 0,
                    orderable: false
                }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data ditemukan",
                    paginate: {
                        previous: "←",
                        next: " →"
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
