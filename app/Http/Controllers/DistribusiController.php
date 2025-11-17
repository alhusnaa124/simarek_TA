<?php

namespace App\Http\Controllers;

use App\Models\Pbb;
use App\Models\User;
use App\Models\Kelompok;
use App\Models\WajibPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistribusiController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter');

        if ($user->role === 'Admin') {
            $dataDistribusi = Pbb::with('wajibPajak.petugas');

            if ($filter === 'sudah') {
                // Petugas sudah diisi (distribusi oleh admin)
                $dataDistribusi->whereHas('wajibPajak', fn($q) => $q->whereNotNull('id_petugas'));
            } elseif ($filter === 'belum') {
                $dataDistribusi->whereHas('wajibPajak', fn($q) => $q->whereNull('id_petugas'));
            }

            $dataDistribusi = $dataDistribusi->get();
        } elseif ($user->role === 'Petugas') {
            $idPetugas = $user->id;
            $dataDistribusi = Pbb::whereHas('wajibPajak', function ($query) use ($idPetugas, $filter) {
                $query->where('id_petugas', $idPetugas);

                if ($filter === 'sudah') {
                    $query->whereNotNull('id_kelompok')->whereNotNull('bagian');
                } elseif ($filter === 'belum') {
                    $query->where(function ($q) {
                        $q->whereNull('id_kelompok')->orWhereNull('bagian');
                    });
                }
            })
                ->with('wajibPajak.petugas')
                ->get();
        } else {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return view('distribusi.distribusi', compact('dataDistribusi'));
    }


    public function edit($no_wp)
    {
        // Ambil data PBB berdasarkan no_wp
        $distribusi = Pbb::with('wajibPajak')->where('no_wp', $no_wp)->firstOrFail();

        // Ambil semua user yang berperan sebagai petugas
        $petugas = User::where('role', 'Petugas')
            ->whereNotNull('label_petugas')
            ->get();

        return view('distribusi.editDistribusi_admin', compact('distribusi', 'petugas'));
    }


    public function update(Request $request, string $no_wp)
    {
        $request->validate([
            'id_petugas' => 'required|exists:users,id', // validasi ke users, bukan petugas
        ]);

        // Ambil PBB berdasar no_wp, sekaligus relasi WajibPajak
        $pbb = Pbb::with('wajibPajak')->where('no_wp', $no_wp)->firstOrFail();

        if (!$pbb->wajibPajak) {
            return redirect()->route('distribusi')->with('error', 'Data Wajib Pajak tidak ditemukan.');
        }

        // Update id_petugas di tabel wajib_pajak
        $wajibPajak = $pbb->wajibPajak;
        $wajibPajak->id_petugas = $request->id_petugas;
        $wajibPajak->save();

        return redirect()->route('distribusi')->with('success', 'Data petugas berhasil diperbarui.');
    }


    public function editKelompok($no_wp)
    {
        $user = Auth::user();

        // Ambil distribusi berdasarkan NOP (nomor wajib pajak)
        $distribusi = Pbb::with('wajibPajak')->where('no_wp', $no_wp)->firstOrFail();

        // Ambil semua kelompok yang ada
        $kelompok = Kelompok::whereHas('wajibPajak', function ($query) use ($user) {
            $query->where('id_petugas', $user->id);
        })->get();


        // Kembalikan data distribusi dan kelompok ke view
        return view('distribusi.editDistribusi_petugas', compact('distribusi', 'kelompok'));
    }


    public function updateKelompok(Request $request, $no_wp)
    {
        // Validasi input
        $request->validate([
            'id_kelompok' => 'required|exists:kelompok,id',
            'bagian' => 'required|string|max:255',
        ]);

        // Cari data Pbb berdasarkan no_wp
        $pbb = Pbb::where('no_wp', $no_wp)->firstOrFail();

        // Cari data WajibPajak yang terkait
        $wajibPajak = WajibPajak::findOrFail($pbb->no_wp);

        // Update id_kelompok dan bagian untuk WP yang sedang diubah
        $wajibPajak->id_kelompok = $request->id_kelompok;
        $wajibPajak->bagian = $request->bagian;
        $wajibPajak->save();

        // Update semua WP yang berada dalam kelompok yang sama
        WajibPajak::where('id_kelompok', $request->id_kelompok)
            ->update(['bagian' => $request->bagian]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('distribusi')
            ->with('success', 'Data kelompok dan bagian berhasil diperbarui untuk semua WP dalam kelompok.');
    }
}
