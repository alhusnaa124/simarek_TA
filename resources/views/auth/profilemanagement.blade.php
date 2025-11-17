@extends('layout')

@section('content')
    <div class="container mx-auto mt-10 px-4">
        <!-- Profile Information Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Informasi Profil</h2>
                <p class="text-gray-600 mt-1">Update informasi akun anda seperti Username dan Email</p>
            </div>

            <form action="{{ route('updateprofile') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           required 
                           value="{{ $user->nama }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           value="{{ $user->email }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Update Password</h2>
                <p class="text-gray-600 mt-1">Pastikan password anda panjang dan aman</p>
            </div>

            <form action="{{ route('updatepassword') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="currentpassword" class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                    <input type="password" 
                           id="currentpassword" 
                           name="currentpassword" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                </div>

                <div>
                    <label for="newpassword" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" 
                           id="newpassword" 
                           name="newpassword" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                </div>

                <div>
                    <label for="newpassword_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" 
                           id="newpassword_confirmation" 
                           name="newpassword_confirmation" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete Account Card -->
        {{-- <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="border-b border-red-200 pb-4 mb-6">
                <h2 class="text-2xl font-bold text-red-900">Hapus Akun</h2>
                <p class="text-red-600 mt-1">Tindakan ini tidak dapat mengembalikan akun anda. Pastikan anda sudah yakin untuk menghapus akun</p>
            </div>

            <form action="#" method="POST" data-confirm="true">
                @csrf
                <div class="bg-red-50 p-4 rounded-lg border border-red-200 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-red-800 text-sm font-medium">
                            Akun yang dihapus tidak dapat dipulihkan. Semua data akan hilang secara permanen.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            onclick="confirmDeleteAccount(event)"
                            class="bg-red-600 text-white px-6 py-2 rounded-lg shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        Hapus Akun
                    </button>
                </div>
            </form>
        </div> --}}
    </div>

@endsection

@push('scripts')
    <!-- Flowbite CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    
    <!-- Toast Notifications Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3"></div>
    
    <script>
        // Flowbite Toast Functions
        function showToast(type, title, message, duration = 3000) {
            const toastContainer = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            
            let iconSvg = '';
            let bgColor = '';
            let textColor = '';
            let iconColor = '';
            
            switch(type) {
                case 'success':
                    bgColor = 'bg-green-50';
                    textColor = 'text-green-800';
                    iconColor = 'text-green-400';
                    iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>`;
                    break;
                case 'error':
                    bgColor = 'bg-red-50';
                    textColor = 'text-red-800';
                    iconColor = 'text-red-400';
                    iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>`;
                    break;
                case 'warning':
                    bgColor = 'bg-yellow-50';
                    textColor = 'text-yellow-800';
                    iconColor = 'text-yellow-400';
                    iconSvg = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>`;
                    break;
            }
            
            const toastHTML = `
                <div id="${toastId}" class="flex items-center w-full max-w-xs p-4 ${bgColor} ${textColor} rounded-lg shadow-lg transform transition-all duration-300 ease-in-out translate-x-full opacity-0" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconColor} rounded-lg">
                        ${iconSvg}
                    </div>
                    <div class="ml-3 text-sm font-medium">
                        <div class="font-semibold">${title}</div>
                        <div class="text-xs mt-1 opacity-90">${message}</div>
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 ${textColor} hover:${textColor} rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" onclick="closeToast('${toastId}')">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            // Animate in
            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                    toast.classList.add('translate-x-0', 'opacity-100');
                }
            }, 100);
            
            // Auto remove
            setTimeout(() => {
                closeToast(toastId);
            }, duration);
        }
        
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }
        
        // Flowbite Modal untuk konfirmasi
        function showConfirmModal(title, message, onConfirm) {
            const modalHTML = `
                <div id="confirm-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-white rounded-lg shadow">
                            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="confirm-modal">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                            </button>
                            <div class="p-6 text-center">
                                <svg class="mx-auto mb-4 text-red-400 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <h3 class="mb-5 text-lg font-normal text-gray-500">${title}</h3>
                                <p class="mb-5 text-sm text-gray-400">${message}</p>
                                <button type="button" onclick="confirmAction()" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                    Ya, Hapus!
                                </button>
                                <button type="button" data-modal-hide="confirm-modal" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('confirm-modal');
            if (existingModal) existingModal.remove();
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Store callback function
            window.confirmAction = onConfirm;
            
            // Show modal
            const modal = new Modal(document.getElementById('confirm-modal'));
            modal.show();
        }

        // Session messages handling
        @if (session('success'))
            showToast('success', 'Berhasil!', '{{ session('success') }}');
        @endif

        @if (session('error'))
            showToast('error', 'Gagal!', '{{ session('error') }}', 5000);
        @endif

        @if ($errors->any())
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '{{ $error }} ';
            @endforeach
            showToast('error', 'Validation Error', errorMessages, 5000);
        @endif

        // Konfirmasi hapus akun
        function confirmDeleteAccount(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            
            showConfirmModal(
                'Apakah Anda yakin?',
                'Akun yang dihapus tidak dapat dipulihkan. Semua data akan hilang secara permanen!',
                function() {
                    form.submit();
                    document.getElementById('confirm-modal').remove();
                }
            );
        }

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (form.getAttribute('data-confirm')) return; // Skip validation for delete form
                
                const requiredFields = form.querySelectorAll('input[required]');
                let emptyFields = [];
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        emptyFields.push(field.previousElementSibling.textContent);
                    }
                });
                
                if (emptyFields.length > 0) {
                    e.preventDefault();
                    showToast('warning', 'Perhatian!', `Mohon lengkapi field: ${emptyFields.join(', ')}`, 4000);
                }
            });
        });
    </script>
@endpush