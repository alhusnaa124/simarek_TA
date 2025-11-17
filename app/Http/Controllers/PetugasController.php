<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $petugas = User::where('role', 'Petugas')->get();

        return view('petugas.petugas', compact('petugas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allLabels = ['Petugas 1', 'Petugas 2', 'Petugas 3', 'Petugas 4', 'Petugas 5'];
        $usedLabels = User::whereNotNull('label_petugas')->pluck('label_petugas')->toArray();
        return view('petugas.tambah', compact('allLabels', 'usedLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'alamat' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'label_petugas' => 'nullable|string|max:10',
        ]);

        // default password
        $password = $request->filled('password') ? $request->password : '12345678';

        try {
            // Jika label_petugas diisi, kosongkan label dari user lain & pindahkan WP-nya
            if ($request->filled('label_petugas')) {
                $petugasLama = User::where('label_petugas', $request->label_petugas)->first();

                if ($petugasLama) {
                    // Kosongkan label dari petugas lama
                    $petugasLama->update(['label_petugas' => null]);

                    // Pindahkan semua WP-nya ke petugas yang akan dibuat
                    // Tapi karena belum ada ID petugas baru, kita tunda sedikit
                    $pindahWpDari = $petugasLama->id;
                }
            }

            // Buat petugas baru
            $namaDepan = explode(' ', $request->nama)[0];
            $newPetugas = User::create([
                'nama' => $request->nama,
                'username' => strtolower($namaDepan),
                'alamat' => $request->alamat,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => 'Petugas',
                'label_petugas' => $request->label_petugas,
            ]);

            // Setelah ID tersedia, update WP
            if (isset($pindahWpDari)) {
                \App\Models\WajibPajak::where('id_petugas', $pindahWpDari)
                    ->update(['id_petugas' => $newPetugas->id]);
            }

            return redirect()->route('petugas.index')->with('success', 'Data petugas berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data petugas. Silakan coba lagi.');
        }
    }



    public function edit($id)
    {
        $petugas = User::findOrFail($id);

        $allLabels = ['Petugas 1', 'Petugas 2', 'Petugas 3', 'Petugas 4', 'Petugas 5'];
        $usedLabels = User::whereNotNull('label_petugas')
            ->where('id', '!=', $id) // Kecuali petugas yang sedang diedit
            ->pluck('label_petugas')
            ->toArray();

        return view('petugas.edit', compact('petugas', 'allLabels', 'usedLabels'));
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'label_petugas' => 'nullable|string|max:10',
        ]);

        try {
            $petugas = User::findOrFail($id);

            // Cek perubahan label_petugas
            if ($request->filled('label_petugas') && $request->label_petugas !== $petugas->label_petugas) {
                $petugasLama = User::where('label_petugas', $request->label_petugas)
                    ->where('id', '!=', $petugas->id)
                    ->first();

                if ($petugasLama) {
                    // Pindahkan WP dari petugas lama ke petugas yang diedit
                    \App\Models\WajibPajak::where('id_petugas', $petugasLama->id)
                        ->update(['id_petugas' => $petugas->id]);

                    // Kosongkan label dari petugas lama
                    $petugasLama->update(['label_petugas' => null]);
                }

                // Set label ke petugas yang baru
                $petugas->label_petugas = $request->label_petugas;
            }

            // Update data umum
            $petugas->update([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'label_petugas' => $petugas->label_petugas,
            ]);

            return redirect()->route('petugas.index')->with('success', 'Data petugas berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data petugas. Silahkan coba lagi.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $petugas = User::findOrFail($id);

            // // Kosongkan label_petugas dulu
            // $petugas->update(['label_petugas' => null]);

            // Baru hapus petugas
            $petugas->delete();

            return redirect()->route('petugas.index')->with('success', 'Petugas berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('petugas.index')->with('error', 'Gagal Menghapus Petugas. Silahkan Coba lagi');
        }
    }
}
