@extends('layouts.guest')

@section('content')
    <div class="min-h-screen bg-cover bg-center flex flex-col items-center justify-center relative"
        style="background-image: url('/images/bg2.jpeg');">

        <!-- Judul di luar kotak login -->
        <div class="text-center mb-6 px-4">
            <h1 class="text-3xl font-bold text-black drop-shadow-md">SIMAREK PBB WONOYOSO</h1>
            <p class="text-black text-sm drop-shadow-sm">
                Sistem Informasi Manajemen Rekapitulasi PBB<br>Desa Wonoyoso
            </p>
        </div>

        <!-- Kotak login -->
        <div class="bg-white/80 px-8 py-3 rounded-2xl shadow-xl w-full max-w-4xl flex flex-col md:flex-row items-center justify-between">

            <!-- Gambar Ilustrasi -->
            <div class="w-full md:w-1/2 hidden md:block">
                <img src="/images/operator.png" alt="Ilustrasi Operator" class="w-full h-auto">
            </div>

            <!-- Form Login -->
            <div class="w-full md:w-1/2 mt-8 md:mt-0">

                <!-- Alert Error (Flowbite) -->
                @if($errors->any())
                    <div id="alert-error" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 border border-red-300" role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div class="ms-3 text-sm font-medium">
                            <strong>Login Gagal!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                        @if($error == 'The provided credentials do not match our records.')
                                            Email atau password tidak sesuai.
                                        @else
                                            {{ $error }}
                                        @endif
    
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-error" aria-label="Close">
                            <span class="sr-only">Tutup</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Alert Success (Flowbite) -->
                @if(session('success'))
                    <div id="alert-success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-300" role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <div class="ms-3 text-sm font-medium">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close">
                            <span class="sr-only">Tutup</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Alert Info untuk logout (Flowbite) -->
                @if(session('info'))
                    <div id="alert-info" class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 border border-blue-300" role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <div class="ms-3 text-sm font-medium">
                            {{ session('info') }}
                        </div>
                        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-info" aria-label="Close">
                            <span class="sr-only">Tutup</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div class="relative">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" required autofocus value="{{ old('email') }}"
                                class="bg-blue-100 text-black text-sm rounded-lg block w-full ps-10 p-3 border border-gray-300 focus:ring-blue-400 focus:border-blue-400 placeholder-gray-600"
                                placeholder="Masukkan Email">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2"
                                        d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="relative">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="bg-blue-100 text-black text-sm rounded-lg block w-full ps-10 p-3 border border-gray-300 focus:ring-blue-400 focus:border-blue-400 placeholder-gray-600"
                                placeholder="Masukkan Password">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Login -->
                    <div class="flex justify-center">
                        <button type="submit"
                            class="w-40 text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 text-center transition">
                            LOGIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script untuk auto-hide alert dan interaksi Flowbite -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts setelah 6 detik
            const alerts = document.querySelectorAll('[id^="alert-"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.style.display !== 'none') {
                        // Animasi fade out
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    }
                }, 6000);
            });

            // Handle manual close buttons
            document.querySelectorAll('[data-dismiss-target]').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-dismiss-target');
                    const target = document.querySelector(targetId);
                    if (target) {
                        target.style.transition = 'opacity 0.3s ease-out';
                        target.style.opacity = '0';
                        setTimeout(() => {
                            target.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    </script>

@endsection