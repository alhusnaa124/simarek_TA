@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('/images/bg.jpeg');">
    <div class="bg-white/70 p-10 rounded-2xl shadow-xl w-full max-w-4xl flex items-center">
        <div class="w-1/2 hidden md:block">
            <img src="/images/operator.png" alt="Ilustrasi" class="w-full">
        </div>

        <div class="w-full md:w-1/2 px-6">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-1">Daftar Akun Baru</h1>
            <p class="text-center text-sm mb-6 text-gray-700">Silakan isi form untuk membuat akun baru</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Username -->
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama -->
                <div class="mb-4">
                    <label for="nama" class="block text-gray-700">Nama Lengkap</label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <label for="alamat" class="block text-gray-700">Alamat</label>
                    <input id="alamat" type="text" name="alamat" value="{{ old('alamat') }}" required
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="block text-gray-700">Role</label>
                    <select id="role" name="role" required
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled selected>Pilih Role</option>
                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Petugas" {{ old('role') == 'Petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="Bendahara" {{ old('role') == 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div> --}}

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full mt-1 rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-600">
                        Sudah punya akun?
                    </a>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">
                        Daftar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
