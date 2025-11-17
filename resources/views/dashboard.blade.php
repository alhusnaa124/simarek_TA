@extends('layout')

@section('content')
    @php
        $role = auth()->user()->role;
    @endphp

    {{-- ADMIN --}}
    @if ($role === 'Admin')
        <div class="container mx-auto px-6">
            <div class="flex justify-end my-6">
                <form class="max-w-sm">
                    <select id="tahun"
                        class="inline-block w-auto pr-10 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                        focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <option value="">Pilih Tahun</option>
                        @foreach ($tahunLabels as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-cyan-400 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">Total SPPT</h3>
                    <p class="text-3xl font-bold" id="totalSPPT">{{ $totalSPPT }} SPPT</p>
                    <p class="text-sm" id="totalNominal">Rp. {{ number_format($totalpajak, 0, ',', '.') }}</p>
                </div>

                <div class="bg-green-400 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">SPPT Lunas</h3>
                    <p class="text-3xl font-bold" id="persenLunas">{{ $persenLunas }}%</p>
                    <p class="text-sm" id="totalLunas">Rp. {{ number_format($totalLunas, 0, ',', '.') }}</p>
                </div>

                <div class="bg-red-400 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">SPPT Terhutang</h3>
                    <p class="text-3xl font-bold" id="persenBelumLunas">{{ $persenBelumLunas }}%</p>
                    <p class="text-sm" id="totalBelumLunas">Rp. {{ number_format($totalBelumLunas, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex justify-center gap-6 mt-6">
                <!-- LINE CHART -->
                <div class="bg-white rounded-xl p-6 shadow-lg content-center max-w-4xl w-full">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4" id="chartTitle">Pendapatan Bulanan</h3>
                    <canvas id="lineChart" class="w-full h-60"></canvas>
                </div>
            </div>
        </div>


        {{-- PETUGAS --}}
    @elseif ($role === 'Petugas')
        <div class="container mx-auto p-4">
            <div class="flex justify-end my-2">
                <form method="GET" class="max-w-sm">
                    <select id="tahun" name="tahun" onchange="this.form.submit()"
                        class="inline-block w-auto pr-10 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                        focus:ring-blue-500 focus:border-blue-500 p-2.5">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunLabels as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}
                            </option>
                        @endforeach
                    </select>
                </form>

            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-indigo-500 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">Total Kelompok WP</h3>
                    <p class="text-3xl font-bold">{{ $totalKelompok }} Kelompok</p>
                </div>

                <div class="bg-cyan-400 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">Total SPPT</h3>
                    <p class="text-3xl font-bold">{{ $totalSPPT }} SPPT</p>
                    <p class="text-sm">Rp. {{ number_format($totalpajak, 0, ',', '.') }}</p>
                </div>

                <div class="bg-green-400 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">SPPT Lunas</h3>
                    <p class="text-3xl font-bold">{{ $persenLunas }}%</p>
                    <p class="text-sm">Rp. {{ number_format($totalLunas, 0, ',', '.') }}</p>
                </div>

                <div class="bg-red-400 text-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold">SPPT Terhutang</h3>
                    <p class="text-3xl font-bold">{{ $persenBelumLunas }}%</p>
                    <p class="text-sm">Rp. {{ number_format($totalBelumLunas, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 max-w-md mx-auto shadow-lg">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Progres Pembayaran</h3>
                <div class="flex justify-center items-center w-full h-60">
                    <canvas id="progressChart" class="mx-auto max-w-[220px] max-h-[220px]"></canvas>
                </div>
                <div class="flex justify-center mt-4 gap-6 text-gray-700">
                    <div class="flex items-center space-x-2">
                        <span class="block w-4 h-4 bg-blue-600 rounded-sm"></span>
                        <span>Lunas ({{ $persenLunas }}%)</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="block w-4 h-4 bg-teal-400 rounded-sm"></span>
                        <span>Belum Lunas ({{ $persenBelumLunas }}%)</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- BENDAHARA --}}
    @elseif ($role === 'Bendahara')
        {{-- Dropdown Tahun di pojok kanan atas --}}
        <div class="flex justify-end mb-4">
            <form>
                <select id="tahun"
                    class="w-auto pr-10 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
            focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">Pilih Tahun</option>
                    @foreach ($tahunLabels as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Grid berisi 2 kartu --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Kartu Chart Setoran --}}
            <div class="bg-white p-4 shadow rounded-lg">
                <h2 class="text-lg font-semibold">Setoran</h2>
                <canvas id="myChart" class="mt-4 h-64"></canvas>
            </div>

            {{-- Kartu Target --}}
            <div class="bg-white p-4 shadow rounded-lg">
                <h2 class="text-lg font-semibold">Target</h2>
                <p class="text-sm text-gray-600 font-bold mt-1">Rp.
                    {{ number_format($totalNominal ?? 0, 0, ',', '.') }}</p>
                <div id="gaugeChart" class="mt-4"></div>
                <div class="text-center mt-4">
                    <p class="text-2xl font-bold">{{ $persenTercapai ?? 0 }}%</p>
                    <p class="text-sm text-gray-600">Rp. {{ number_format($totalLunas ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @else
        <p>Role tidak ditemukan, silakan hubungi admin.</p>
    @endif

    {{-- SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @if ($role === 'Admin')
        <script>
            // Inisialisasi Chart
            let lineChart;
            const ctxLine = document.getElementById('lineChart').getContext('2d');

            // Data awal dari PHP (data per bulan untuk tahun terbaru atau keseluruhan)
            const initialBulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            let initialBulanData = @json($bulanPendapatan ?? array_fill(0, 12, 0));

            // Buat chart awal
            function createChart(labels, data, title) {
                if (lineChart) {
                    lineChart.destroy();
                }

                lineChart = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: data,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Inisialisasi chart dengan data awal
            createChart(initialBulanLabels, initialBulanData, 'Pendapatan Bulanan');

            // Format rupiah
            function formatRupiah(angka) {
                return 'Rp. ' + angka.toLocaleString('id-ID');
            }

            // Event listener untuk dropdown tahun
            document.getElementById('tahun').addEventListener('change', function() {
                const tahunTerpilih = this.value;

                if (tahunTerpilih === '') {
                    // Jika tidak ada tahun dipilih, tampilkan data keseluruhan
                    location.reload();
                    return;
                }

                // AJAX request
                fetch(`/dashboard/data-tahun/${tahunTerpilih}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update cards
                        document.getElementById('totalSPPT').textContent = data.totalSPPT + ' SPPT';
                        document.getElementById('totalNominal').textContent = formatRupiah(data.totalNominal);
                        document.getElementById('persenLunas').textContent = data.persenLunas + '%';
                        document.getElementById('totalLunas').textContent = formatRupiah(data.totalLunas);
                        document.getElementById('persenBelumLunas').textContent = data.persenBelumLunas + '%';
                        document.getElementById('totalBelumLunas').textContent = formatRupiah(data.totalBelumLunas);

                        // Update chart
                        const chartTitle = `Pendapatan Bulanan ${tahunTerpilih}`;
                        document.getElementById('chartTitle').textContent = chartTitle;
                        createChart(data.bulanLabels, data.bulanPendapatan, chartTitle);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengambil data');
                    });
            });
        </script>
    @elseif ($role === 'Petugas')
        <script>
            const progressCtx = document.getElementById('progressChart')?.getContext('2d');
            const persenLunas = @json($persenLunas);
            const persenBelumLunas = @json($persenBelumLunas);

            if (progressCtx) {
                new Chart(progressCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Lunas', 'Belum Lunas'],
                        datasets: [{
                            data: [persenLunas, persenBelumLunas],
                            backgroundColor: ['#2563EB', '#14B8A6'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        </script>
    @elseif ($role === 'Bendahara')
        <script>
            // Gauge Chart (Target Tercapai - Bendahara)
            if (document.querySelector("#gaugeChart")) {
                const gaugeSeries = @json($persenTercapai ?? 0);
                new ApexCharts(document.querySelector("#gaugeChart"), {
                    chart: {
                        type: 'radialBar',
                        height: 250,
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            hollow: {
                                size: '70%'
                            },
                            track: {
                                background: '#e0e0e0',
                                strokeWidth: '100%'
                            },
                            dataLabels: {
                                show: false
                            }
                        }
                    },
                    series: [gaugeSeries],
                    colors: ['#4F46E5'],
                    labels: ['']
                }).render();
            }

            // Bar Chart untuk Setoran per Petugas

            window.onload = function() {
                const myChartCtx = document.getElementById('myChart')?.getContext('2d');
                if (myChartCtx) {
                    const petugasLabels = @json($petugasLabels ?? []);
                    const petugasSetoran = @json($petugasSetoran ?? []);
                    console.log(petugasLabels, petugasSetoran); // debug

                    new Chart(myChartCtx, {
                        type: 'bar',
                        data: {
                            labels: petugasLabels,
                            datasets: [{
                                label: 'Setoran',
                                data: petugasSetoran,
                                backgroundColor: '#3B82F6',
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp. ' + Number(value).toLocaleString('id-ID', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            });
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.raw || 0;
                                            return 'Setoran: Rp. ' + Number(value).toLocaleString('id-ID', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            };
        </script>
    @endif
@endsection
