<?php

namespace App\Http\Controllers;

use App\Models\SetoranPBB;
use Illuminate\Http\Request;
use App\Models\FormulirTarikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Hamcrest\Core\Set;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetoranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'belum');

        $query = SetoranPBB::with('formulirTarikans.kelompok', 'verifikator', 'petugas');

        // Role Petugas hanya melihat setoran sendiri
        if ($user->role === 'Petugas') {
            $query->where('id_petugas', $user->id);
        }

        // Filter status diverifikasi atau belum
        if ($status === 'sudah') {
            $query->whereNotNull('id_verifikator');
        } else {
            $query->whereNull('id_verifikator');
        }

        $setoran = $query->get();

        // === Pemrosesan Gabungan atau Individual ===
        $processedSetoran = collect();
        foreach ($setoran as $item) {
            $formulir = $item->formulirTarikan ?? collect();

            $grouped = $formulir->groupBy('kelompok.id');

            foreach ($grouped as $group) {
                $tanggalUnik = $group->pluck('tgl_bayar')->unique();

                if ($tanggalUnik->count() === 1 && $group->count() > 1) {
                    $first = $item->replicate();
                    $first->gabungan = true;
                    $first->kelompok = $group->first()->kelompok;
                    $first->tahun = $group->pluck('tahun')->sort()->join(', ');
                    $first->total = $group->sum('total');
                    $first->formulir_ids = $group->pluck('id')->join(', ');
                    $processedSetoran->push($first);
                } else {
                    foreach ($group as $f) {
                        $clone = $item->replicate();
                        $clone->gabungan = false;
                        $clone->kelompok = $f->kelompok;
                        $clone->tahun = $f->tahun;
                        $clone->total = $f->total;
                        $clone->formulir_id = $f->id;
                        $processedSetoran->push($clone);
                    }
                }
            }
        }

        return view('setoran.setoran', [
            'setoran' => $processedSetoran
        ]);
    }

    public function show($id)
    {
        $setoran = SetoranPBB::with(['verifikator', 'petugas'])->findOrFail($id);

        // Ambil semua formulir yang terkait
        $formulirList = FormulirTarikan::with('kelompok')
            ->where('id_setoran', $id)
            ->get();

        return view('setoran.detail', compact('setoran', 'formulirList'));
    }


    public function create()
    {
        $user = Auth::user();

        // Ambil formulir lunas & belum disetorkan
        $formulirLunas = FormulirTarikan::with(['kelompok', 'kelompok.wajibPajak'])
            ->where('status', 'lunas')
            ->whereNull('id_setoran')
            ->whereNull('bukti')
            ->whereHas('kelompok.wajibPajak', function ($query) use ($user) {
                $query->where('id_petugas', $user->id);
            })
            ->get();

        // Ambil formulir yang ditolak
        $formulirDitolak = FormulirTarikan::with(['kelompok', 'kelompok.wajibPajak'])
            ->whereHas('setoran', function ($query) use ($user) {
                $query->where('status', 'Di Tolak')
                    ->where('id_petugas', $user->id);
            })
            ->get();

        // Gabungkan
        $formulirGabungan = $formulirLunas->merge($formulirDitolak);

        // Kelompokkan berdasarkan kelompok ID
        $grouped = $formulirGabungan->groupBy('kelompok.id');
        $filtered = collect();

        foreach ($grouped as $group) {
            // Cek apakah semua formulir punya tanggal bayar yang sama
            $tanggalUnik = $group->pluck('tgl_bayar')->unique();

            // Jika hanya 1 tanggal dan lebih dari 1 formulir, anggap gabungan
            if ($tanggalUnik->count() === 1 && $group->count() > 1) {
                $first = $group->first();
                $first->gabungan = true;
                $first->tahun_digabung = $group->pluck('tahun')->toArray();
                $first->ids_digabung = $group->pluck('id')->toArray();
                $first->tgl_bayar = $tanggalUnik->first();
                $first->total = $group->sum('total');
                $filtered->push($first);
            } else {
                // Tidak bisa digabung, tampilkan satu per satu
                foreach ($group as $item) {
                    $item->gabungan = false;
                    $filtered->push($item);
                }
            }
        }

        $formulir = $filtered->sortByDesc('created_at');

        return view('setoran.tambah', compact('formulir'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'formulir_id' => 'required|array|min:1',
            'formulir_id.*' => 'exists:formulir_tarikan,id',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $formulirTerpilih = FormulirTarikan::whereIn('id', $request->formulir_id)
            ->where('status', 'lunas')
            ->whereNull('id_setoran')
            ->get();

        if ($formulirTerpilih->isEmpty()) {
            return back()->withErrors(['formulir_ids' => 'Formulir tidak valid atau sudah pernah disetorkan.']);
        }

        $jumlahSetor = $formulirTerpilih->sum('total');
        $buktiPath = $request->file('bukti')->store('bukti_setoran', 'public');

        DB::beginTransaction();
        try {
            $setoran = SetoranPBB::create([
                'id_petugas' => Auth::id(),
                'tanggal_setor' => now()->toDateString(),
                'jumlah_setor' => $jumlahSetor,
                'tahun' => now()->year,
                'bukti' => $buktiPath,
            ]);

            FormulirTarikan::whereIn('id', $request->formulir_id)
                ->update(['id_setoran' => $setoran->id]);

            DB::commit();

            return redirect()->route('setoran')->with('success', 'Setoran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan setoran: ' . $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $setoran = SetoranPBB::findOrFail($id);
        return view('setoran.edit', compact('setoran'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Di terima,Di tolak',
            'catatan' => 'nullable|string',
        ]);

        $setoran = SetoranPBB::findOrFail($id);
        $setoran->status = ucfirst($request->status);
        $setoran->catatan = $request->catatan;
        $setoran->id_verifikator = Auth::id(); // Menyimpan ID user yang login
        $setoran->save();

        return redirect()->route('setoran')->with('success', 'Data setoran berhasil diperbarui.');
    }

    public function cetak($id)
    {
        $setoran = SetoranPBB::with('petugas')->findOrFail($id);

        if ($setoran->status !== 'Di terima') {
            return redirect()->route('setoran')->with('error', 'Setoran belum diverifikasi.');
        }

        $pdf = Pdf::loadView('setoran.kwitansi', compact('setoran'));
        return $pdf->stream('kwitansi-setoran-' . $setoran->id . '.pdf');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
