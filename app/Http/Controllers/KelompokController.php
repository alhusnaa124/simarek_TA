<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\WajibPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KelompokController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $kelompok = Kelompok::whereHas('wajibPajak', function ($query) use ($user) {
            $query->where('id_petugas', $user->id);
        })->with('wajibPajak.pbb')->get();

        $tahunList = []; // untuk semua tahun yang muncul

        foreach ($kelompok as $k) {
            $tahunPbb = [];

            foreach ($k->wajibPajak as $wp) {
                foreach ($wp->pbb as $pbb) {
                    $tahun = $pbb->tahun;
                    $tahunPbb[$tahun] = ($tahunPbb[$tahun] ?? 0) + 1;
                    if (!in_array($tahun, $tahunList)) {
                        $tahunList[] = $tahun;
                    }
                }
            }

            ksort($tahunPbb); // urutkan berdasarkan tahun
            $k->tahun_pbb = $tahunPbb;

            $k->alamat_singkat = optional($k->wajibPajak->firstWhere('kepala_keluarga', true))->alamat_wp
                ? Str::words(optional($k->wajibPajak->firstWhere('kepala_keluarga', true))->alamat_wp, 2, '')
                : '-';
        }

        sort($tahunList); // urutkan tahun untuk header tabel
        return view('kelompok.kelompok', compact('kelompok', 'tahunList'));
    }



    public function create()
    {
        $kelompok = Kelompok::with('wajibPajak')->get();
        return view('distribusi.editDistribusi_petugas', compact('kelompok'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:50',
            'alamat_wp' => 'required|string',
        ]);

        $nama = $request->nama_kelompok;
        $alamat_wp = $request->alamat_wp;

        // Cari WP berdasarkan nama & alamat WP (bukan hanya nama saja!)
        $wp = WajibPajak::where('nama_wp', $nama)
            ->where('alamat_wp', $alamat_wp)
            ->latest()
            ->first();

        if (!$wp) {
            return response()->json([
                'message' => 'Wajib Pajak tidak ditemukan.',
            ], 404);
        }

        $alamat_singkat = implode(' ', array_slice(explode(' ', $alamat_wp), 0, 4));

        $kelompok_sama = Kelompok::where('nama_kelompok', $nama)
            ->whereHas('wajibPajak', function ($query) use ($alamat_wp) {
                $query->where('alamat_wp', $alamat_wp);
            })
            ->exists();

        if ($kelompok_sama && !$request->paksa) {
            return response()->json([
                'message' => 'Kelompok dengan nama dan alamat yang sama sudah ada.',
            ], 409);
        }

        $kelompok = Kelompok::create([
            'nama_kelompok' => $nama,
        ]);

        $wp->id_kelompok = $kelompok->id;
        $wp->kepala_keluarga = true;
        $wp->save();

        return response()->json([
            'id' => $kelompok->id,
            'text' => $kelompok->nama_kelompok,
            'alamat_singkat' => $alamat_singkat,
        ]);
    }




    public function edit($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        return view('kelompok.edit', compact('kelompok'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:50|alpha',
        ], [
            'nama_kelompok.alpha' => 'Nama kelompok hanya boleh berisi huruf',
        ]);

        $kelompok = Kelompok::findOrFail($id);
        $kelompok->update([
            'nama_kelompok' => $request->nama_kelompok,
        ]);

        return redirect()->route('kelompok.index')->with('success', 'Data kelompok berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kelompok = Kelompok::findOrFail($id);

        WajibPajak::where('id_kelompok', $kelompok->id)->update(['id_kelompok' => null]);

        $kelompok->delete();

        return redirect()->route('kelompok.index')->with('success', 'Data kelompok berhasil dihapus.');
    }
}
