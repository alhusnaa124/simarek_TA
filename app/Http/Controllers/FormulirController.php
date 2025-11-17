<?php

namespace App\Http\Controllers;

use App\Models\Pbb;
use App\Models\Kelompok;
use App\Models\WajibPajak;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FormulirTarikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FormulirController extends Controller
{
    private function hitungStatistikKelompok($kelompokList, $tahun = null)
    {
        foreach ($kelompokList as $k) {
            $totalPbbCount = 0;

            foreach ($k->wajibPajak as $wp) {
                $pbbFiltered = $tahun
                    ? $wp->pbb->where('tahun', $tahun)
                    : $wp->pbb;

                $totalPbbCount += $pbbFiltered->count();
            }

            $k->jumlah_pbb = $totalPbbCount;

            if ($tahun && $k->formulirTarikan && $k->formulirTarikan->tahun != $tahun) {
                $k->total_tarikan = 0;
                $k->id_formulir = null;
            } else {
                $k->total_tarikan = $k->formulirTarikan->total ?? 0;
                $k->id_formulir = $k->formulirTarikan->id ?? null;
            }
        }

        return $kelompokList;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tahunDipilih = $request->input('tahun');

        // 🔹 Ambil semua formulir (yang sudah pernah dibuat) sesuai tahun dan petugas
        $formulirs = FormulirTarikan::with(['kelompok.wajibPajak.pbb'])
            ->whereHas('kelompok.wajibPajak', function ($query) use ($user) {
                $query->where('id_petugas', $user->id);
            })
            ->when($tahunDipilih, function ($query) use ($tahunDipilih) {
                $query->where('tahun', $tahunDipilih);
            })
            ->get();

        // 🔹 Ambil semua kelompok untuk dicek siapa saja yang belum punya formulir tahun tertentu
        $kelompokList = Kelompok::whereHas('wajibPajak', function ($query) use ($user) {
            $query->where('id_petugas', $user->id);
        })->with(['wajibPajak.pbb'])->get();

        // 🔹 Hitung kelompok yang belum punya formulir di tahun tertentu
        $kelompokBelumAdaFormulir = $kelompokList->map(function ($k) {
            $tahunBelum = $k->wajibPajak
                ->flatMap->pbb
                ->pluck('tahun')
                ->unique()
                ->filter(function ($tahun) use ($k) {
                    return !FormulirTarikan::where('id_kelompok', $k->id)
                        ->where('tahun', $tahun)
                        ->exists();
                })
                ->values();

            $k->tahun_belum_ada_formulir = $tahunBelum;
            return $k;
        })->filter(fn($k) => $k->tahun_belum_ada_formulir->isNotEmpty());

        return view('formulirTarikan.formulir', compact('formulirs', 'kelompokBelumAdaFormulir', 'tahunDipilih', 'kelompokList'));
    }



    public function show(Request $request, $id)
    {
        $tahun = $request->input('tahun');

        $user = Auth::user();
        // 🔹 Ambil semua formulir (yang sudah pernah dibuat) sesuai tahun dan petugas
        $formulirs = FormulirTarikan::with(['kelompok.wajibPajak.pbb'])
            ->whereHas('kelompok.wajibPajak', function ($query) use ($user) {
                $query->where('id_petugas', $user->id);
            })
            ->when($tahun, function ($query) use ($tahun) {
                $query->where('tahun', $tahun);
            })
            ->get();

        $kelompokList = Kelompok::whereHas('wajibPajak', function ($query) use ($user) {
            $query->where('id_petugas', $user->id);
        })->with(['wajibPajak.pbb' => function ($query) use ($tahun) {
            if ($tahun) {
                $query->where('tahun', $tahun);
            }
        }, 'formulirTarikan'])->get();

        $kelompokList = $this->hitungStatistikKelompok($kelompokList, $tahun);
        $kelompokBelumAdaFormulir = $kelompokList->map(function ($k) {
            $tahunBelum = $k->wajibPajak
                ->flatMap->pbb
                ->pluck('tahun')
                ->unique()
                ->filter(function ($tahun) use ($k) {
                    return !FormulirTarikan::where('id_kelompok', $k->id)
                        ->where('tahun', $tahun)
                        ->exists();
                })
                ->values();

            $k->tahun_belum_ada_formulir = $tahunBelum;
            return $k;
        })->filter(fn($k) => $k->tahun_belum_ada_formulir->isNotEmpty());

        $kelompok = $kelompokList->firstWhere('id', $id);
        if (!$kelompok) {
            abort(404, 'Kelompok tidak ditemukan');
        }

        $wajibPajaks = $kelompok->wajibPajak;
        $kepalaKeluarga = $wajibPajaks->where('kepala_keluarga', true)->first();
        $tahunDipilih = $tahun;

        return view('formulirTarikan.formulir', compact(
            'kelompokList',
            'kelompok',
            'wajibPajaks',
            'kepalaKeluarga',
            'formulirs',
            'kelompokBelumAdaFormulir',
            'tahunDipilih'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_pembayaran' => 'required|date',
            'id_kelompok' => 'required|exists:kelompok,id',
            'tahun' => 'nullable|integer',
        ]);

        $kelompok = Kelompok::with('wajibPajak.pbb')->findOrFail($request->id_kelompok);
        $tahunFilter = $request->input('tahun');

        // Filter PBB belum lunas sesuai tahunFilter jika ada
        $pbbBelumLunas = $kelompok->wajibPajak
            ->flatMap(function ($wp) use ($tahunFilter) {
                return $wp->pbb
                    ->filter(function ($pbb) use ($tahunFilter) {
                        $belumLunas = !$pbb->isLunas();
                        return $tahunFilter ? ($belumLunas && $pbb->tahun == $tahunFilter) : $belumLunas;
                    });
            });

        if ($pbbBelumLunas->isEmpty()) {
            return back()->withErrors(['Tidak ada tagihan PBB yang bisa ditarik.']);
        }

        $tahunFormulir = $tahunFilter ?? $pbbBelumLunas->max('tahun');

        if (FormulirTarikan::where('id_kelompok', $kelompok->id)->where('tahun', $tahunFormulir)->exists()) {
            return back()->withErrors(['Formulir untuk tahun ' . $tahunFormulir . ' sudah dibuat.']);
        }

        $total = $pbbBelumLunas->sum(fn($pbb) => $pbb->pajak_terhutang + $pbb->dharma_tirta);

        $lastNumber = FormulirTarikan::count();
        $newId = 'FR-' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);

        $formulir = FormulirTarikan::create([
            'id' => $newId,
            'id_kelompok' => $kelompok->id,
            'tahun' => $tahunFormulir,
            'total' => $total,
            'jadwal_pembayaran' => $request->jadwal_pembayaran,
            'status' => 'belum lunas',
        ]);

        // Attach pivot berdasarkan id pbb (primary key baru)
        $pivotData = [];
        foreach ($pbbBelumLunas as $pbb) {
            $pivotData[$pbb->id] = ['tahun' => $pbb->tahun];
        }
        $formulir->pbbs()->attach($pivotData);

        return redirect()->route('formulir')->with('success', 'Formulir berhasil dibuat.');
    }

    public function cetak($id)
    {
        $formulir = FormulirTarikan::with('kelompok')->findOrFail($id);
        $kelompok = $formulir->kelompok;

        // Ambil semua formulir milik kelompok ini
        $formulirs = FormulirTarikan::where('id_kelompok', $kelompok->id)
            ->with(['pbbs' => function ($q) {
                $q->with('wajibPajak');
            }])
            ->orderBy('tahun')
            ->get();

        // Cek apakah semua formulir belum lunas
        $semuaBelumLunas = $formulirs->every(fn($f) => $f->status === 'belum lunas');

        if ($semuaBelumLunas) {
            // Gabungkan semua pbbs dari semua tahun yang status "belum lunas"
            $pbbs = collect();

            foreach ($formulirs as $f) {
                $pbbs = $pbbs->merge(
                    $f->pbbs->filter(fn($pbb) => $pbb->pivot->tahun == $f->tahun)
                );
            }

            $pbbs = $pbbs->sortBy('pivot.tahun')->values();
        } else {
            // Tampilkan hanya data tahun dari formulir yang diklik
            $pbbs = $formulir->pbbs()
                ->with('wajibPajak')
                ->wherePivot('tahun', $formulir->tahun)
                ->get()
                ->filter(fn($pbb) => $pbb->tahun == $formulir->tahun);
        }

        $kepalaKeluarga = $kelompok->wajibPajak()->where('kepala_keluarga', true)->first();

        return view('formulirTarikan.cetak', compact(
            'formulir',
            'kelompok',
            'pbbs',
            'kepalaKeluarga'
        ));
    }


    public function exportPdf($id)
    {
        $formulir = FormulirTarikan::with('kelompok')->findOrFail($id);
        $kelompok = $formulir->kelompok;

        // Ambil semua formulir milik kelompok ini
        $formulirs = FormulirTarikan::where('id_kelompok', $kelompok->id)
            ->orderBy('tahun')
            ->get();

        // Cek apakah semua formulir belum lunas
        $semuaBelumLunas = $formulirs->every(fn($f) => $f->status === 'belum lunas');

        if ($semuaBelumLunas) {
            // Ambil kepala keluarga untuk mendapatkan alamat/info utama
            $kepalaKeluarga = $kelompok->wajibPajak()->where('kepala_keluarga', true)->first();

            // Ambil semua wajib pajak dalam kelompok ini
            $wajibPajakIds = $kelompok->wajibPajak()->pluck('id');
            $allTahun = $formulirs->pluck('tahun')->unique();

            // Ambil unique PBB milik wajib pajak dalam kelompok
            $uniquePbbs = Pbb::with('wajibPajak')
                ->whereIn('no_wp', $wajibPajakIds)
                ->get()
                ->groupBy('nop') // Group by NOP untuk menghindari duplikasi
                ->map(function ($group) {
                    return $group->first(); // Ambil yang pertama dari setiap group
                });

            // Duplikasi untuk setiap tahun
            $pbbs = collect();
            foreach ($allTahun as $tahun) {
                foreach ($uniquePbbs as $pbb) {
                    // Buat array associative untuk menghindari reference issue
                    $pbbData = [
                        'id' => $pbb->id,
                        'nop' => $pbb->nop,
                        'tahun' => $tahun,
                        'luas_tnh' => $pbb->luas_tnh,
                        'pajak_terhutang' => $pbb->pajak_terhutang,
                        'dharma_tirta' => $pbb->dharma_tirta,
                        'wajibPajak' => $pbb->wajibPajak,
                    ];

                    // Convert ke object untuk kompatibilitas dengan view
                    $pbbObject = (object) $pbbData;
                    $pbbs->push($pbbObject);
                }
            }

            $pbbs = $pbbs->sortBy(['tahun', 'nop'])->values();
        } else {
            // Tampilkan hanya data tahun dari formulir yang diklik
            $pbbs = $formulir->pbbs()->with('wajibPajak')->get()->map(function ($pbb) use ($formulir) {
                $pbb->tahun = $formulir->tahun;
                return $pbb;
            });

            $kepalaKeluarga = $kelompok->wajibPajak()->where('kepala_keluarga', true)->first();
        }

        $pdf = Pdf::loadView('formulirTarikan.cetak', compact(
            'formulir',
            'kelompok',
            'pbbs',
            'kepalaKeluarga'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('formulir_tarikan_' . $kelompok->id . '.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'tgl_bayar' => 'required|date',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $formulir = FormulirTarikan::findOrFail($id);

        $updateData = [
            'status' => 'lunas',
            'tgl_bayar' => $validated['tgl_bayar']
        ];

        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_pembayaran', 'public');
            $updateData['bukti'] = $buktiPath;
        }

        $formulir->update($updateData);

        return redirect()->route('formulir.data')
            ->with('success', 'Status pembayaran berhasil diperbarui');
    }

    public function updateConfig(Request $request)
    {
        $jadwal = $request->input('jadwal');

        $data = [
            'jadwal_pembayaran' => $jadwal,
            'ttd_kepala_desa' => ''
        ];

        if ($request->hasFile('ttd')) {
            $file = $request->file('ttd');
            $filename = 'ttd_kades_2025.png';
            $file->move(public_path('ttd'), $filename);

            $data['ttd_kepala_desa'] = $filename;
        } else {
            $old = json_decode(Storage::get('public/formulir_config.json'), true);
            $data['ttd_kepala_desa'] = $old['ttd_kepala_desa'] ?? '';
        }

        Storage::put('public/formulir_config.json', json_encode($data, JSON_PRETTY_PRINT));

        return back()->with('success', 'Berhasil disimpan!');
    }

    public function edit()
    {
        $config = json_decode(Storage::get('public/formulir_config.json'), true);

        return view('formulirTarikan.pengaturan', compact('config'));
    }

    public function update(Request $request)
    {
        $data = [
            'jadwal_pembayaran' => $request->input('jadwal'),
            'ttd_kepala_desa' => '',
            'nama_kepalaDesa' => $request->input('nama_kepalaDesa')
        ];

        if ($request->hasFile('ttd')) {
            $file = $request->file('ttd');
            $filename = 'ttd_kades_2025.png';
            $file->move(public_path('ttd'), $filename);
            $data['ttd_kepala_desa'] = $filename;
        } else {
            $old = json_decode(Storage::get('public/formulir_config.json'), true);
            $data['ttd_kepala_desa'] = $old['ttd_kepala_desa'] ?? '';
        }

        Storage::put('public/formulir_config.json', json_encode($data, JSON_PRETTY_PRINT));

        return redirect()->route('formulir')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
