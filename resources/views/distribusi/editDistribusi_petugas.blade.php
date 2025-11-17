@extends('layout')

@section('content')
    <div class="container mx-auto p-6">
        <div class="mb-6 flex items-center">
            <h1 class="text-2xl font-bold">DISTRIBUSI PBB → Edit Kelompok</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('pembayaran.distribusi.updateKelompok', $distribusi->no_wp) }}"
                class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="alamat_wp" class="block text-sm font-medium text-gray-700 mb-1">Alamat WP</label>
                    <input type="text" id="alamat_wp" name="alamat_wp" value="{{ $distribusi->wajibPajak->alamat_wp }}"
                        readonly class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nop" class="block text-sm font-medium text-gray-700 mb-1">NOP</label>
                        <input type="text" id="nop" name="nop" value="{{ $distribusi->nop }}" readonly
                            class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3">
                    </div>
                    <div>
                        <label for="nama_wp" class="block text-sm font-medium text-gray-700 mb-1">Nama WP</label>
                        <input type="text" id="nama_wp" name="nama_wp" value="{{ $distribusi->wajibPajak->nama_wp }}"
                            readonly class="w-full bg-gray-100 border border-gray-300 rounded-md py-2 px-3">
                    </div>

                    <div>
                        <label for="kelompok" class="block text-sm font-medium text-gray-700 mb-1">Kelompok</label>
                        <select id="kelompok" name="id_kelompok"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($kelompok as $kel)
                                @php
                                    $kepala = $kel->wajibPajak->firstWhere('kepala_keluarga', true);
                                    $bagian = $kepala?->bagian;
                                    $alamatSingkat = $kepala
                                        ? implode(' ', array_slice(explode(' ', $kepala->alamat_wp), 0, 4))
                                        : '-';
                                @endphp
                                <option value="{{ $kel->id }}"
                                    {{ $distribusi->wajibPajak->id_kelompok == $kel->id ? 'selected' : '' }}>
                                    {{ $kel->nama_kelompok }}{{ $bagian ? ' - ' . $bagian : '' }}{{ $alamatSingkat ? ' - ' . $alamatSingkat : '' }}
                                </option>
                            @endforeach
                        </select>

                        <div class="mt-2 text-sm text-gray-500">Jika kelompok tidak ada, ketikkan nama kelompok baru dan
                            tekan Enter untuk menambahkannya.</div>
                    </div>

                    <div>
                        <label for="bagian" class="block text-sm font-medium text-gray-700 mb-1">Bagian</label>
                        <select id="bagian" name="bagian" class="w-full border border-gray-300 rounded-md py-2 px-3">
                            <option value="RT001" {{ $distribusi->wajibPajak->bagian == 'RT001' ? 'selected' : '' }}>RT001
                            </option>
                            <option value="RT002" {{ $distribusi->wajibPajak->bagian == 'RT002' ? 'selected' : '' }}>RT002
                            </option>
                            <option value="RT003" {{ $distribusi->wajibPajak->bagian == 'RT003' ? 'selected' : '' }}>RT003
                            </option>
                            <option value="RT004" {{ $distribusi->wajibPajak->bagian == 'RT004' ? 'selected' : '' }}>RT004
                            </option>
                            <option value="RT005" {{ $distribusi->wajibPajak->bagian == 'RT005' ? 'selected' : '' }}>RT005
                            </option>
                            <option value="Luar Desa"
                                {{ $distribusi->wajibPajak->bagian == 'Luar Desa' ? 'selected' : '' }}>Luar Desa</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('distribusi') }}"
                        class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600">BATAL</a>
                    <button type="submit"
                        class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">SIMPAN</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#kelompok').select2({
                tags: true,
                tokenSeparators: [','],
                createTag: function(params) {
                    return {
                        id: params.term + '-' + Date.now(),
                        text: params.term,
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.push(tag);
                }
            });

            $('#kelompok').on('select2:select', function(e) {
                var selected = e.params.data;

                if (selected.newTag) {
                    $.ajax({
                        url: '{{ route('kelompok.store') }}',
                        method: 'POST',
                        data: {
                            nama_kelompok: selected.text,
                            alamat_wp: '{{ $distribusi->wajibPajak->alamat_wp }}', // kirim alamat WP aktif
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            let label = response.text;
                            if (response.alamat_singkat) {
                                label += ' - ' + response.alamat_singkat;
                            }
                            $('#kelompok').append(new Option(label, response.id, true, true))
                                .trigger('change');

                            Swal.fire('Berhasil!', 'Kelompok baru berhasil ditambahkan.',
                                'success');
                        },
                        error: function(xhr) {
                            if (xhr.status === 409) {
                                Swal.fire({
                                    title: 'Tambah Kelompok Sama?',
                                    text: 'Kelompok dengan nama dan alamat yang sama sudah ada. Tambah baru dengan alamat berbeda?',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ya, tambahkan baru'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: '{{ route('kelompok.store') }}',
                                            method: 'POST',
                                            data: {
                                                nama_kelompok: selected.text,
                                                alamat_wp: '{{ $distribusi->wajibPajak->alamat_wp }}', // tetap kirim alamat
                                                paksa: true,
                                                _token: '{{ csrf_token() }}'
                                            },
                                            success: function(response) {
                                                let label = response.text;
                                                if (response
                                                    .alamat_singkat) {
                                                    label += ' - ' +
                                                        response
                                                        .alamat_singkat;
                                                }
                                                $('#kelompok').append(
                                                        new Option(label,
                                                            response.id,
                                                            true, true))
                                                    .trigger('change');

                                                Swal.fire('Berhasil!',
                                                    'Kelompok baru berhasil ditambahkan.',
                                                    'success');
                                            },
                                            error: function() {
                                                Swal.fire('Gagal!',
                                                    'Gagal menambahkan kelompok.',
                                                    'error');
                                            }
                                        });
                                    } else {
                                        $('#kelompok').val(null).trigger('change');
                                    }
                                });
                            } else {
                                Swal.fire('Gagal!', xhr.responseJSON.message ||
                                    'Terjadi kesalahan.', 'error');
                            }
                        }
                    });

                }
            });
        });
    </script>
@endpush
