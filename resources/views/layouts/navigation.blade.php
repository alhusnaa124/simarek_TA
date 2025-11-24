<nav class="bg-blue-400 p-4 text-white flex justify-between items-center">
    <div class="flex items-center">
        <!-- Hamburger Menu Button -->
        <button id="sidebarToggle" class=" md:hidden mr-4 p-2 rounded hover:bg-blue-500 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
        <a href="{{ route('dashboard') }}" class="block text-xl font-bold hover:bg-sky-600 px-2 py-1 rounded">DASHBOARD</a>
    </div>

    <!-- Bagian avatar + dropdown -->
    <div class="relative flex items-center mr-7">
        @if (auth()->check())
            <!-- Avatar -->
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}" alt="Profile"
                id="dropdownToggle" class="w-8 h-8 rounded-full cursor-pointer">

            <!-- Dropdown Menu -->
            <div id="dropdownMenu"
                class="absolute right-0 top-full mt-2 min-w-[150px] bg-white text-black rounded shadow-md z-10 hidden">
                <!-- Header -->
                <div class="px-4 py-2 border-b font-semibold">
                    {{ auth()->user()->nama }}
                </div>

                <!-- Profile Link -->
                <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-200">
                    Profile
                </a>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-200">
                        Logout
                    </button>
                </form>
            </div>
        @endif
    </div>
</nav>

<script>
    // Dropdown functionality
    const toggle = document.getElementById('dropdownToggle');
    const menu = document.getElementById('dropdownMenu');

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Optional: klik di luar menutup dropdown
        document.addEventListener('click', function (e) {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    }

    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const content = document.querySelector('.content');

    if (sidebarToggle && sidebar && content) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
            
            // Adjust content margin
            if (sidebar.classList.contains('-translate-x-full')) {
                content.style.marginLeft = '0';
                content.style.width = '100%';
            } else {
                content.style.marginLeft = '250px';
                content.style.width = 'calc(100% - 250px)';
            }
        });
    }
</script>