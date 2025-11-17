<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BendaharaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bendahara = User::where('role', 'Bendahara')->get();

        return view('bendahara.bendahara', compact('bendahara'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bendahara.tambah');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'alamat' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nama.regex' => 'Nama tidak boleh mengandung angka atau simbol.',

            'alamat.required' => 'Alamat wajib diisi.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        $password = $request->filled('password') ? $request->password : '12345678';

        try {
            $namaDepan = explode(' ', $request->nama)[0];

            User::create([
                'nama' => $request->nama,
                'username' => strtolower($namaDepan),
                'alamat' => $request->alamat,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => 'Bendahara',
            ]);

            return redirect()->route('bendahara.index')->with('success', 'Data bendahara berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data bendahara. Silakan coba lagi.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bendahara = User::findOrFail($id);
        return view('bendahara.edit', compact('bendahara'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        try {
            $bendahara = User::findOrFail($id);

            $bendahara->nama = $request->nama;
            $bendahara->alamat = $request->alamat;
            $bendahara->email = $request->email;

            $bendahara->save();

            return redirect()->route('bendahara.index')->with('success', 'Data bendahara berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('bendahara.index')->with('error', 'Gagal Mengupdate data bendahara. silahkan coba lagi');
        }
    }
    public function destroy(string $id)
    {
        $jumlahBendahara = User::where('role', 'bendahara')->count();

        // Cek apakah jumlah bendahara lebih dari 1
        if ($jumlahBendahara <= 1) {
            return redirect()->route('bendahara.index')->with('error', 'Tidak dapat menghapus. Minimal harus ada satu bendahara.');
        }

        $bendahara = User::findOrFail($id);
        $bendahara->delete();

        return redirect()->route('bendahara.index')->with('success', 'Bendahara berhasil dihapus!');
    }
}
