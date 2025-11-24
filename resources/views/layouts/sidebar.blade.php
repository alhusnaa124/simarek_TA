<div id="sidebar" class="w-[250px] fixed h-screen p-4 overflow-y-auto shadow-md bg-blue-400 z-10 
    transform -translate-x-full md:translate-x-0 
    transition-transform duration-300 ease-in-out">
    <h2 class="text-lg font-bold mb-4 text-white">Menu</h2>

    @if (auth()->check())
        @php
            $role = auth()->user()->role;
        @endphp

        @if ($role === 'Admin')
            <div class="mb-4">
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">DATA PBB</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li>
                        <a href="{{ route('wajib.pajak') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('wajib.pajak') ? 'bg-sky-600 text-white' : '' }}">
                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 3v4a1 1 0 0 1-1 1H5m4 8h6m-6-4h6m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                            </svg>
                            Wajib Pajak dan PBB
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('distribusi') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('distribusi') ? 'bg-sky-600 text-white' : '' }}">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                            </svg>
                            Distribusi Wajib Pajak
                        </a>
                    </li>
                </ul>
            </div>
            <div class="mb-4">
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">MANAGEMEN</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li>
                        <a href="{{ route('bendahara.index') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('bendahara.index') ? 'bg-sky-600 text-white' : '' }}">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z" />
                            </svg>
                            Data Bendahara
                        </a>
                    </li>
                    <li><a href="{{ route('petugas.index') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('petugas.index') ? 'bg-sky-600 text-white' : '' }}"><svg
                                class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>Data Petugas</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">REKAPITULASI</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li><a href="{{ route('rekap') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('rekap') ? 'bg-sky-600 text-white' : '' }}"><svg
                                class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
                            </svg>Rekapitulasi</a>
                    </li>
                </ul>
            </div>
        @elseif($role === 'Petugas')
            <div class="mb-4">
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">DATA PBB</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li>
                        <a href="{{ route('wajib.pajak') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('wajib.pajak') ? 'bg-sky-600 text-white' : '' }}">
                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 3v4a1 1 0 0 1-1 1H5m4 8h6m-6-4h6m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                            </svg>
                            Wajib Pajak dan PBB
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('distribusi') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('distribusi') ? 'bg-sky-600 text-white' : '' }}">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                            </svg>
                            Distribusi Wajib Pajak
                        </a>
                    </li>
                    <li><a href="{{ route('kelompok.index') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('kelompok*') ? 'bg-sky-600 text-white' : '' }}"><svg
                                class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            Kelompok</a></li>
                </ul>
            </div>
            <div class="mb-4">
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">PENCATATAN</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li><a href="{{ route('formulir') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('formulir') ? 'bg-sky-600 text-white' : '' }}"><svg
                                class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                    d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                            </svg>
                            Tarikan</a></li>
                    <li><a href="{{ route('pembayaran.index') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('pembayaran.index') ? 'bg-sky-600 text-white' : '' }}"><svg
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M2.25 6.75c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 0 1 2.25 17.25V6.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8.25c-1.243 0-2.25 1.007-2.25 2.25s1.007 2.25 2.25 2.25 2.25-1.007 2.25-2.25-1.007-2.25-2.25-2.25z" />
                            </svg>
                            Pembayaran</a></li>
                    <li><a href="{{ route('setoran') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('setoran') ? 'bg-sky-600 text-white' : '' }}"><svg
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12H9m0 0 2.25 2.25M9 12l2.25-2.25M19.5 3.75v16.5a.75.75 0 0 1-1.2.6L16.5 19.5l-1.8 1.35a.75.75 0 0 1-.9 0L12 19.5l-1.8 1.35a.75.75 0 0 1-.9 0L7.5 19.5l-1.8 1.35a.75.75 0 0 1-1.2-.6V3.75a.75.75 0 0 1 1.2-.6L7.5 4.5l1.8-1.35a.75.75 0 0 1 .9 0L12 4.5l1.8-1.35a.75.75 0 0 1 .9 0L16.5 4.5l1.8-1.35a.75.75 0 0 1 1.2.6z" />
                            </svg>
                            Setoran</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">REKAPITULASI</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li><a href="{{ route('rekapPetugas') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('rekapPetugas') ? 'bg-sky-600 text-white' : '' }}"><svg
                                class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
                            </svg>Rekapitulasi</a>
                    </li>
                </ul>
            </div>
        @elseif($role === 'Bendahara')
            <div class="mb-4">
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">PENCATATAN</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li><a href="{{ route('setoran') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('setoran') ? 'bg-sky-600 text-white' : '' }}"><svg
                                xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12H9m0 0 2.25 2.25M9 12l2.25-2.25M19.5 3.75v16.5a.75.75 0 0 1-1.2.6L16.5 19.5l-1.8 1.35a.75.75 0 0 1-.9 0L12 19.5l-1.8 1.35a.75.75 0 0 1-.9 0L7.5 19.5l-1.8 1.35a.75.75 0 0 1-1.2-.6V3.75a.75.75 0 0 1 1.2-.6L7.5 4.5l1.8-1.35a.75.75 0 0 1 .9 0L12 4.5l1.8-1.35a.75.75 0 0 1 .9 0L16.5 4.5l1.8-1.35a.75.75 0 0 1 1.2.6z" />
                            </svg>Setoran</a></li>
                </ul>
            </div>
            <div class="mb-4">
                <h3 class="font-semibold bg-sky-200 text-black p-2 rounded-t-md">REKAPITULASI</h3>
                <ul class="bg-white shadow-md rounded-b-md">
                    <li><a href="{{ route('rekap') }}"
                            class="flex items-center gap-2 py-2 px-3 hover:bg-blue-200 {{ request()->routeIs('rekap') ? 'bg-sky-600 text-white' : '' }}"><svg
                                class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
                            </svg>Rekapitulasi</a>
                    </li>
                </ul>
            </div>
        @endif
    @endif
</div>