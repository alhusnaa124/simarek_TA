<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pbb;
use App\Models\User;
use App\Models\SetoranPBB;
use App\Models\WajibPajak;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use App\Models\FormulirTarikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'Petugas') {
            abort(403, 'Akses hanya untuk petugas.');
        }

        $filterTahun = $request->input('tahun');
        $filterBagian = $request->input('bagian');

        $daftarTahun = Pbb::whereHas('wajibPajak', fn($q) => $q->where('id_petugas', $user->id))
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $daftarBagian = WajibPajak::where('id_petugas', $user->id)
            ->whereNotNull('bagian')->select('bagian')->distinct()->pluck('bagian');

        $pbbList = Pbb::whereHas('wajibPajak', fn($q) => $q->where('id_petugas', $user->id))
            ->when($filterTahun, fn($q) => $q->where('tahun', $filterTahun))
            ->with('wajibPajak')->get();

        $rekapWilayah = $pbbList
            ->groupBy(fn($pbb) => optional($pbb->wajibPajak)->bagian ?: 'Belum Didistribusikan')
            ->map(function ($group, $bagian) {
                $sppt = $group->count();
                $pajak_terhutang = $group->sum('pajak_terhutang');
                $dharma_tirta = $group->sum('dharma_tirta');
                $jumlah = $pajak_terhutang + $dharma_tirta;

                return (object)[
                    'bagian' => $bagian,
                    'sppt' => $sppt,
                    'pajak_terhutang' => $pajak_terhutang,
                    'dharma_tirta' => $dharma_tirta,
                    'jumlah' => $jumlah,
                    'ikut_total' => $bagian !== 'Belum Didistribusikan',
                ];
            })->values();

        $rekapLunas = FormulirTarikan::with(['kelompok.wajibPajak'])
            ->where('status', 'Lunas')
            ->whereHas('kelompok.wajibPajak', fn($q) => $q->where('id_petugas', $user->id))
            ->when($filterTahun, function ($q) use ($filterTahun) {
                $q->whereHas('kelompok.wajibPajak.pbb', fn($q2) => $q2->where('tahun', $filterTahun));
            })
            ->when($filterBagian, function ($q) use ($filterBagian) {
                $q->whereHas('kelompok.wajibPajak', fn($q2) => $q2->where('bagian', $filterBagian));
            })
            ->get();

        $setoranPetugas = SetoranPBB::whereHas('formulirTarikans.kelompok.wajibPajak', fn($q) => $q->where('id_petugas', $user->id))
            ->where('status', 'Di Terima')
            ->get();

        return view('rekap.rekapPetugas', compact(
            'rekapWilayah',
            'rekapLunas',
            'setoranPetugas',
            'user',
            'filterTahun',
            'filterBagian',
            'daftarTahun',
            'daftarBagian'
        ));
    }

    public function exportRekapPerBagian(Request $request)
    {
        $user = Auth::user();
        $filterTahun = $request->input('tahun');

        $pbbList = Pbb::whereHas('wajibPajak', function ($q) use ($user) {
            $q->where('id_petugas', $user->id);
        })
            ->when($filterTahun, function ($q) use ($filterTahun) {
                $q->where('tahun', $filterTahun);
            })
            ->with('wajibPajak')
            ->get();

        // Kelompokkan berdasarkan bagian
        $grouped = $pbbList->groupBy(function ($pbb) {
            return optional($pbb->wajibPajak)->bagian ?: 'Belum Didistribusikan';
        });

        // Hitung distribusi vs belum distribusi
        $sudahDistribusi = $pbbList->filter(function ($item) {
            return $item->wajibPajak && $item->wajibPajak->bagian;
        });

        $belumDistribusi = $pbbList->filter(function ($item) {
            return !$item->wajibPajak || !$item->wajibPajak->bagian;
        });

        // Ringkasan data untuk PDF
        $totalDistribusi = $sudahDistribusi->count();
        $totalBelumDistribusi = $belumDistribusi->count();
        $totalNominalDistribusi = $sudahDistribusi->sum(fn($item) => $item->pajak_terhutang + $item->dharma_tirta);
        $totalNominalBelumDistribusi = $belumDistribusi->sum(fn($item) => $item->pajak_terhutang + $item->dharma_tirta);

        // Kirim ke PDF
        $pdf = Pdf::loadView('rekap.export-perbagian', [
            'grouped' => $grouped,
            'filterTahun' => $filterTahun,
            'totalDistribusi' => $totalDistribusi,
            'totalBelumDistribusi' => $totalBelumDistribusi,
            'totalNominalDistribusi' => $totalNominalDistribusi,
            'totalNominalBelumDistribusi' => $totalNominalBelumDistribusi,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('rekap_per_bagian_' . now()->format('Ymd_His') . '.pdf');
    }




    public function indexBendahara(Request $request)
    {
        $user = Auth::user();

        $petugasList = User::where('role', 'Petugas')->orderBy('nama')->get();
        $filterPetugas = $request->input('petugas');
        $filterTanggalMulai = $request->input('tanggal_mulai');
        $filterTanggalSelesai = $request->input('tanggal_selesai');
        $filterRekap = $request->input('filter_rekap', 'semua');
        $filterTahun = $request->input('tahun');

        $daftarTahun = Pbb::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        $pbbQuery = Pbb::with('wajibPajak.Petugas');
        if ($filterPetugas) {
            $pbbQuery->whereHas('wajibPajak', fn($q) => $q->where('id_petugas', $filterPetugas));
        }
        if ($filterTahun) {
            $pbbQuery->where('tahun', $filterTahun);
        }

        // Semua data (termasuk belum distribusi)
        $semuaPbb = (clone $pbbQuery)->get();
        $totalSemuaSppt = $semuaPbb->count();
        $totalSemuaPajakTerhutang = $semuaPbb->sum('pajak_terhutang');
        $totalSemuaDharmaTirta = $semuaPbb->sum('dharma_tirta');
        $totalSemuaJumlah = $totalSemuaPajakTerhutang + $totalSemuaDharmaTirta;

        // Data yang sudah didistribusikan
        $pbbList = (clone $pbbQuery)->whereHas('wajibPajak', function ($q) use ($filterPetugas) {
            $q->whereNotNull('bagian');
            if ($filterPetugas) {
                $q->where('id_petugas', $filterPetugas);
            }
        })->get();

        $rekapWilayah = $pbbList->groupBy(fn($pbb) => optional($pbb->wajibPajak->Petugas)->id)
            ->map(function ($group) {
                $petugas = $group->first()->wajibPajak->Petugas;
                $bagianDetail = $group->groupBy(fn($pbb) => optional($pbb->wajibPajak)->bagian)
                    ->map(function ($bagianGroup, $bagian) {
                        return (object) [
                            'bagian' => $bagian,
                            'sppt' => $bagianGroup->count(),
                            'pajak_terhutang' => $bagianGroup->sum('pajak_terhutang'),
                            'dharma_tirta' => $bagianGroup->sum('dharma_tirta'),
                            'jumlah' => $bagianGroup->sum('pajak_terhutang') + $bagianGroup->sum('dharma_tirta'),
                        ];
                    });

                return (object) [
                    'id_petugas' => optional($petugas)->id,
                    'nama_petugas' => optional($petugas)->nama ?? 'Tidak Diketahui',
                    'total_sppt' => $group->count(),
                    'total_pajak_terhutang' => $group->sum('pajak_terhutang'),
                    'total_dharma_tirta' => $group->sum('dharma_tirta'),
                    'total_jumlah' => $group->sum('pajak_terhutang') + $group->sum('dharma_tirta'),
                    'bagian_detail' => $bagianDetail,
                ];
            });

        $setoranQuery = SetoranPBB::with('petugas')->where('status', 'Di Terima');
        if ($filterPetugas) $setoranQuery->where('id_petugas', $filterPetugas);
        if ($filterTanggalMulai) $setoranQuery->whereDate('tanggal_setor', '>=', $filterTanggalMulai);
        if ($filterTanggalSelesai) $setoranQuery->whereDate('tanggal_setor', '<=', $filterTanggalSelesai);
        if ($filterTahun) $setoranQuery->whereYear('tanggal_setor', $filterTahun); // <-- Tambahan penting

        $setoran = $setoranQuery->get();

        $setoranPerPetugas = $setoran->groupBy('id_petugas')->map(function ($group) {
            return (object) [
                'id_petugas' => $group->first()->id_petugas,
                'nama_petugas' => optional($group->first()->petugas)->nama,
                'total_setoran' => $group->sum('jumlah_setor'),
                'detail_setoran' => $group,
            ];
        });

        // Distribusi
        $totalSppt = $rekapWilayah->sum('total_sppt');
        $totalPajakTerhutang = $rekapWilayah->sum('total_pajak_terhutang');
        $totalDharmaTirta = $rekapWilayah->sum('total_dharma_tirta');
        $totalJumlah = $rekapWilayah->sum('total_jumlah');
        $totalSetoran = $setoran->sum('jumlah_setor');
        $selisih = $totalJumlah - $totalSetoran;

        // Semua data
        $totalSemuaLunas = (clone $pbbQuery)
            ->where('status', 'lunas')
            ->sum(DB::raw('pajak_terhutang + dharma_tirta'));

        $totalSemuaSelisih = $totalSemuaJumlah - $totalSemuaLunas;


        $persenDistribusi = $totalSemuaJumlah > 0 ? ($totalJumlah / $totalSemuaJumlah * 100) : 0;

        return view('rekap.rekap', compact(
            'petugasList',
            'rekapWilayah',
            'setoran',
            'filterPetugas',
            'filterTanggalMulai',
            'filterTanggalSelesai',
            'filterRekap',
            'filterTahun',
            'daftarTahun',
            'totalSppt',
            'totalPajakTerhutang',
            'totalDharmaTirta',
            'totalJumlah',
            'setoranPerPetugas',
            'totalSetoran',
            'selisih',
            'totalSemuaSppt',
            'totalSemuaPajakTerhutang',
            'totalSemuaDharmaTirta',
            'totalSemuaJumlah',
            'totalSemuaLunas',
            'totalSemuaSelisih',
            'persenDistribusi'
        ));
    }

    public function exportWajibPajak(Request $request)
    {
        $status = $request->input('status');
        $tahun = $request->input('tahun');
        $user = Auth::user();

        if (!$status || !$tahun) {
            return redirect()->back()->with('error', 'Status dan tahun harus dipilih');
        }

        try {
            $pbbList = Pbb::with('wajibPajak')
                ->where('tahun', $tahun)
                ->where('status', $status)
                ->when($user->role === 'Petugas', function ($query) use ($user) {
                    $query->whereHas('wajibPajak', function ($q) use ($user) {
                        $q->where('id_petugas', $user->id);
                    });
                })
                ->get();

            if ($pbbList->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data untuk diekspor');
            }

            $pdf = Pdf::loadView('rekap.export-wp', [
                'pbbList' => $pbbList,
                'status' => $status,
                'tahun' => $tahun
            ])->setPaper('A4', 'landscape');

            $statusFileName = $status === 'lunas' ? 'Lunas' : 'Belum_Lunas';
            $fileName = "WP_{$statusFileName}_{$tahun}.pdf";

            return $pdf->stream($fileName);
        } catch (\Throwable $e) {
            Log::error('Export PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }





    public function cetakPdf(Request $request)
    {
        $user = Auth::user();

        // Ambil user role petugas untuk dropdown
        $petugasList = User::where('role', 'Petugas')->orderBy('nama')->get();

        // Filter dari request
        $filterPetugas = $request->input('petugas');
        $filterTanggalMulai = $request->input('tanggal_mulai');
        $filterTanggalSelesai = $request->input('tanggal_selesai');
        $filterRekap = $request->input('filter_rekap', 'semua');

        // Data yang akan dikirim ke PDF
        $data = [
            'rekapWilayah' => collect(),
            'setoran' => collect(),
            'totalSppt' => 0,
            'totalPajakTerhutang' => 0,
            'totalDharmaTirta' => 0,
            'totalJumlah' => 0,
            'totalSetoran' => 0,
            'selisih' => 0,
            'filterPetugas' => $filterPetugas,
            'filterTanggalMulai' => $filterTanggalMulai,
            'filterTanggalSelesai' => $filterTanggalSelesai,
            'filterRekap' => $filterRekap,
            'petugasList' => $petugasList,
            'tanggalCetak' => Carbon::now()->format('d/m/Y H:i:s'),
            'namaPetugas' => $filterPetugas ? User::find($filterPetugas)?->nama : 'Semua Petugas'
        ];

        // Jika memerlukan data rekap wilayah
        if ($filterRekap == 'semua' || $filterRekap == 'pbb_wilayah') {
            $pbbQuery = Pbb::with('wajibPajak.Petugas');

            if ($filterPetugas && $filterPetugas != 'semua') {
                $pbbQuery->whereHas('wajibPajak', function ($query) use ($filterPetugas) {
                    $query->where('id_petugas', $filterPetugas);
                });
            }

            $pbbList = $pbbQuery->whereHas('wajibPajak', function ($query) {
                $query->whereNotNull('bagian');
            })->get();

            // Kelompokkan berdasarkan nama petugas dan bagian (wilayah)
            $rekapWilayah = $pbbList->groupBy(function ($pbb) {
                $petugas = optional($pbb->wajibPajak->Petugas)->nama;
                $bagian = optional($pbb->wajibPajak)->bagian;
                return $petugas . '|' . $bagian;
            })->map(function ($group, $key) {
                [$nama_petugas, $bagian] = explode('|', $key);
                return (object)[
                    'nama_petugas' => $nama_petugas,
                    'bagian' => $bagian,
                    'sppt' => $group->count(),
                    'pajak_terhutang' => $group->sum('pajak_terhutang'),
                    'dharma_tirta' => $group->sum('dharma_tirta'),
                    'jumlah' => $group->sum('pajak_terhutang') + $group->sum('dharma_tirta'),
                ];
            });

            $data['rekapWilayah'] = $rekapWilayah;
            $data['totalSppt'] = $rekapWilayah->sum('sppt');
            $data['totalPajakTerhutang'] = $rekapWilayah->sum('pajak_terhutang');
            $data['totalDharmaTirta'] = $rekapWilayah->sum('dharma_tirta');
            $data['totalJumlah'] = $rekapWilayah->sum('jumlah');
        }

        // Jika memerlukan data setoran
        if ($filterRekap == 'semua' || $filterRekap == 'setoran') {
            $setoranQuery = SetoranPBB::with('petugas')->where('status', 'Di Terima');

            if ($filterPetugas && $filterPetugas != 'semua') {
                $setoranQuery->where('id_petugas', $filterPetugas);
            }
            if ($filterTanggalMulai) {
                $setoranQuery->whereDate('tanggal_setor', '>=', $filterTanggalMulai);
            }
            if ($filterTanggalSelesai) {
                $setoranQuery->whereDate('tanggal_setor', '<=', $filterTanggalSelesai);
            }

            $setoran = $setoranQuery->orderBy('tanggal_setor', 'desc')->get();
            $data['setoran'] = $setoran;
            $data['totalSetoran'] = $setoran->sum('jumlah_setor');
        }

        // Hitung selisih
        $data['selisih'] = $data['totalJumlah'] - $data['totalSetoran'];

        // Generate PDF
        $pdf = Pdf::loadView('rekap.rekap-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        // Nama file PDF
        $fileName = 'Rekap_PBB_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->stream($fileName);
    }
}
