<?php

namespace App\Http\Controllers;

use App\Models\Pbb;
use App\Models\WajibPajak;
use Illuminate\Http\Request;
use App\Models\FormulirTarikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'belum');

        $query = FormulirTarikan::with('kelompok')
            ->whereHas('kelompok.wajibPajak', function ($q) use ($user) {
                $q->where('id_petugas', $user->id);
            });

        if ($status === 'sudah') {
            $query->whereNotNull('tgl_bayar');
        } else {
            $query->whereNull('tgl_bayar');
        }

        $formulirs = $query->get();

        $processedFormulirs = collect();
        $processedKelompok = [];

        foreach ($formulirs as $formulir) {
            $kelompokFormulirs = $formulirs->where('id_kelompok', $formulir->id_kelompok);

            // Cek apakah semua status sama
            $isAllSameStatus = $status === 'belum'
                ? $kelompokFormulirs->every(fn($f) => $f->status === 'belum lunas')
                : $kelompokFormulirs->every(fn($f) => $f->status === 'lunas');

            // Cek apakah semua tgl_bayar sama (untuk status sudah)
            $isSameDate = $status === 'sudah'
                ? $kelompokFormulirs->pluck('tgl_bayar')->unique()->count() === 1
                : true;

            // Gabungan hanya jika semua status sama dan tanggal bayar sama (jika status sudah)
            $isGabungan = $isAllSameStatus && $isSameDate;

            if ($isGabungan && in_array($formulir->id_kelompok, $processedKelompok)) {
                continue;
            }

            if ($isGabungan) {
                $processedKelompok[] = $formulir->id_kelompok;
            }

            $formulir->is_gabungan = $isGabungan;
            $formulir->total_gabungan = $isGabungan ? $kelompokFormulirs->sum('total') : $formulir->total;
            $formulir->tahun_gabungan = $isGabungan ? $kelompokFormulirs->pluck('tahun')->sort()->join(', ') : $formulir->tahun;
            $formulir->formulir_ids = $isGabungan ? $kelompokFormulirs->pluck('id')->join(', ') : $formulir->id;
            $formulir->jumlah_formulir = $kelompokFormulirs->count();

            $processedFormulirs->push($formulir);
        }

        return view('pembayaran.pembayaran', compact('processedFormulirs', 'status'));
    }



    public function show($id)
    {
        $formulir = FormulirTarikan::with('kelompok')->findOrFail($id);

        if (!$formulir) {
            return response()->json(['message' => 'Formulir tidak ditemukan'], 404);
        }

        // Implementasi logika gabungan untuk total
        $kelompok = $formulir->kelompok;
        $formulirs = FormulirTarikan::where('id_kelompok', $kelompok->id)
            ->orderBy('tahun')
            ->get();

        $semuaBelumLunas = $formulirs->every(fn($f) => $f->status === 'belum lunas');

        $total = $semuaBelumLunas ? $formulirs->sum('total') : $formulir->total;

        return response()->json([
            'id' => $formulir->id,
            'total' => $total,
            'kelompok' => [
                'nama_kelompok' => $formulir->kelompok->nama_kelompok ?? '-',
            ]
        ]);
    }

    public function detail($id)
    {
        $formulir = FormulirTarikan::with(['kelompok', 'petugas'])->findOrFail($id);
        $kelompok = $formulir->kelompok;

        // Ambil semua formulir dalam kelompok yang statusnya LUNAS
        $formulirs = FormulirTarikan::where('id_kelompok', $kelompok->id)
            ->whereNotNull('tgl_bayar')
            ->with(['pbbs' => fn($q) => $q->with('wajibPajak')])
            ->orderBy('tahun')
            ->get();

        // Cek apakah ini gabungan:
        // -> semua lunas, dan semua dibayar pada tanggal yang sama
        $isGabungan = $formulirs->count() > 1
            && $formulirs->every(fn($f) => $f->status === 'lunas')
            && $formulirs->pluck('tgl_bayar')->unique()->count() === 1;

        // Ambil wajib pajak dalam kelompok
        $wajibPajak = WajibPajak::with('petugas')->where('id_kelompok', $kelompok->id)->get();

        if ($isGabungan) {
            // Jika gabungan, ambil semua PBB dari tahun-tahun formulir yang lunas
            $tahunLunas = $formulirs->pluck('tahun')->unique();

            $pbb = Pbb::with('wajibPajak')
                ->whereIn('no_wp', $wajibPajak->pluck('id'))
                ->whereIn('tahun', $tahunLunas)
                ->get()
                ->sortBy(['tahun', 'nop'])
                ->values();

            // Gabungkan info formulir
            $formulir->is_gabungan = true;
            $formulir->tahun_gabungan = $tahunLunas->sort()->join(', ');
            $formulir->formulir_ids = $formulirs->pluck('id')->join(', ');
            $formulir->total = $formulirs->sum('total');
            $formulir->tgl_bayar = $formulirs->first()->tgl_bayar;
        } else {
            // Jika bukan gabungan, ambil PBB dari formulir ini saja
            $pbb = $formulir->pbbs()->with('wajibPajak')->get()->map(function ($item) use ($formulir) {
                $item->tahun = $formulir->tahun;
                return $item;
            });

            $formulir->is_gabungan = false;
        }

        return view('pembayaran.detail', compact('formulir', 'wajibPajak', 'pbb'));
    }


    public function cetak($id)
    {
        $formulir = FormulirTarikan::with('kelompok', 'petugas')->findOrFail($id);
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
            // Ambil semua wajib pajak dalam kelompok ini
            $wajibPajak = WajibPajak::with('petugas')
                ->where('id_kelompok', $formulir->id_kelompok)
                ->get();

            $allTahun = $formulirs->pluck('tahun')->unique();

            // Ambil unique PBB milik wajib pajak dalam kelompok
            $uniquePbbs = Pbb::with('wajibPajak')
                ->whereIn('no_wp', $wajibPajak->pluck('id'))
                ->get()
                ->groupBy('nop')
                ->map(function ($group) {
                    return $group->first();
                });

            // Duplikasi untuk setiap tahun
            $pbb = collect();
            foreach ($allTahun as $tahun) {
                foreach ($uniquePbbs as $pbbItem) {
                    $pbbData = [
                        'id' => $pbbItem->id,
                        'nop' => $pbbItem->nop,
                        'tahun' => $tahun,
                        'luas_tnh' => $pbbItem->luas_tnh,
                        'pajak_terhutang' => $pbbItem->pajak_terhutang,
                        'dharma_tirta' => $pbbItem->dharma_tirta,
                        'wajibPajak' => $pbbItem->wajibPajak,
                    ];

                    $pbbObject = (object) $pbbData;
                    $pbb->push($pbbObject);
                }
            }

            $pbb = $pbb->sortBy(['tahun', 'nop'])->values();

            // Tambahkan data gabungan
            $formulir->formulir_ids = $formulirs->pluck('id')->implode(', ');
            $formulir->tahun_gabungan = $pbb->pluck('tahun')->unique()->sort()->implode(', ');
        } else {
            // Tampilkan hanya data tahun dari formulir yang diklik
            $wajibPajak = WajibPajak::with('petugas')
                ->where('id_kelompok', $formulir->id_kelompok)
                ->get();

            $pbb = $formulir->pbbs()->with('wajibPajak')->get()->map(function ($pbbItem) use ($formulir) {
                $pbbItem->tahun = $formulir->tahun;
                return $pbbItem;
            });

            // Tetapkan default untuk non-gabungan
            $formulir->formulir_ids = $formulir->id;
            $formulir->tahun_gabungan = $formulir->tahun;
        }

        // Gabungkan semua data ke dalam satu variabel $pembayaran
        $pembayaran = $formulir;
        $pembayaran->wajib_pajak = $wajibPajak;
        $pembayaran->pbb = $pbb;

        // Render PDF
        $pdf = Pdf::loadView('pembayaran.kwitansi', compact('pembayaran'));

        return $pdf->stream('Kwitansi_PBB_' . $pembayaran->id . '.pdf');
    }


    public function create(Request $request)
    {
        $formulirId = $request->formulir_id;
        $formulir = FormulirTarikan::findOrFail($formulirId);

        return view('pembayaran.tambah', compact('formulir'));
    }

    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $formulir = FormulirTarikan::findOrFail($id);

        $request->validate([
            'metode' => 'required|in:tunai,transfer',
            'tgl_bayar' => 'required|date',
            'bukti' => 'required_if:metode,transfer|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = [
            'tgl_bayar' => $request->tgl_bayar,
            'status' => 'lunas',
            'id_petugas' => $user->id,
        ];

        if ($request->metode === 'transfer' && $request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('bukti_transfer', 'public');
        }

        $kelompok = $formulir->kelompok;
        $formulirs = FormulirTarikan::where('id_kelompok', $kelompok->id)->get();
        $semuaBelumLunas = $formulirs->every(fn($f) => $f->status === 'belum lunas');

        if ($semuaBelumLunas) {
            FormulirTarikan::where('id_kelompok', $kelompok->id)->update($data);

            $wajibPajaks = $kelompok->wajibPajak()->with('pbb')->get();

            foreach ($wajibPajaks as $wp) {
                foreach ($wp->pbb as $pbb) {
                    $pbb->status = 'lunas';
                    $pbb->save();
                }
            }
        } else {
            $formulir->update($data);

            $wajibPajaks = $formulir->kelompok->wajibPajak()->with('pbb')->get();

            foreach ($wajibPajaks as $wp) {
                foreach ($wp->pbb as $pbb) {
                    $pbb->status = 'lunas';
                    $pbb->save();
                }
            }
        }


        return redirect()->route('pembayaran.detail', $formulir->id)->with('success', 'Pembayaran berhasil ditambahkan.');
    }


    public function destroy($id)
    {
        $formulir = FormulirTarikan::findOrFail($id);

        // Hapus file bukti jika ada
        if ($formulir->bukti) {
            Storage::delete($formulir->bukti);
        }

        // Reset status pembayaran
        $resetData = [
            'tgl_bayar' => null,
            'bukti' => null,
            'status' => 'belum lunas',
        ];

        // Jika ada formulir lain dalam kelompok yang sudah lunas, 
        // maka hapus semua pembayaran dalam kelompok
        $kelompok = $formulir->kelompok;
        $formulirs = FormulirTarikan::where('id_kelompok', $kelompok->id)->get();
        $adaYangLunas = $formulirs->some(fn($f) => $f->status === 'lunas');

        if ($adaYangLunas) {
            // Reset semua formulir dalam kelompok
            FormulirTarikan::where('id_kelompok', $kelompok->id)->update($resetData);
        } else {
            // Reset hanya formulir yang dipilih
            $formulir->update($resetData);
        }

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dihapus.');
    }
}
