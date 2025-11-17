<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $remember = $request->boolean('remember');

        // Jalankan autentikasi manual
        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek jika role petugas tapi tidak punya label_petugas
            if ($user->role === 'Petugas' && is_null($user->label_petugas)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun petugas belum memiliki label, silakan hubungi admin.',
                ]);
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


    // Menampilkan data user yang sedang login
    public function profile()
    {
        $user = Auth::user();
        return view('auth.profilemanagement', compact('user'));
    }

    // Update data user yang sedang login
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $pengguna = User::find($user->id);

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ]);
        $pengguna->nama = $request->nama;
        $pengguna->email = $request->email;

        $pengguna->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    // Update password hanya jika password lama benar
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $pengguna = User::find($user->id);

        // Validasi input
        $request->validate([
            'currentpassword' => ['required'],
            'newpassword' => ['required', 'confirmed'],
        ]);
        // Cek apakah password lama cocok dengan yang ada di database
        if (!Hash::check($request->currentpassword, $pengguna->password)) {
            return back()->withErrors(['currentpassword' => 'Password lama tidak sesuai.']);
        }

        // Update password baru jika valid
        $pengguna->password = Hash::make($request->newpassword);
        $pengguna->save();

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }

    // Hapus akun user yang sedang login dan logout otomatis
    // public function deleteAccount(Request $request)
    // {
    //     $user = Auth::user();

    //     if ($user) {
    //         Auth::logout(); // Logout user sebelum dihapus
    //         $user->delete(); // Hapus user dari database

    //         $request->session()->invalidate();
    //         $request->session()->regenerateToken();

    //         return redirect('/')->with('success', 'Akun Anda telah dihapus.');
    //     }

    //     return redirect()->back()->with('error', 'Gagal menghapus akun.');
    // }
}
