<?php

namespace App\Http\Controllers;

use App\Models\Pbb;
use App\Models\User;
use App\Models\WajibPajak;
use App\Models\FormulirTarikan;
use App\Models\SetoranPBB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'Admin') {
            return $this->dashboardAdmin();
        } elseif ($user->role === 'Petugas') {
            return $this->dashboardPetugas($user->id);
        } elseif ($user->role === 'Bendahara') {
            return $this->dashboardBendahara();
        } else {
            abort(403, 'Anda tidak memiliki akses ke dashboard ini.');
        }
    }

    // Method baru untuk AJAX request
    public function getDataByYear($tahun)
    {
        $user = Auth::user();

        if ($user->role !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Filter data berdasarkan tahun
        $totalSPPT = Pbb::where('tahun', $tahun)->count();
        $totalpajak = Pbb::where('tahun', $tahun)->sum('pajak_terhutang');
        $totalDharmaTirta = Pbb::where('tahun', $tahun)->sum('dharma_tirta');
        $totalNominal = $totalpajak + $totalDharmaTirta;

        $pbbFiltered = Pbb::with('wajibPajak')->where('tahun', $tahun)->get();
        $totalLunas = $this->hitungTotalLunas($pbbFiltered);
        $totalBelumLunas = $totalNominal - $totalLunas;

        $persenLunas = $totalNominal > 0 ? round(($totalLunas / $totalNominal) * 100, 2) : 0;
        $persenBelumLunas = 100 - $persenLunas;

        // Data pendapatan bulanan untuk tahun yang dipilih
        $perBulan = FormulirTarikan::selectRaw('MONTH(tgl_bayar) as bulan, SUM(total) as total')
            ->whereYear('tgl_bayar', $tahun)
            ->whereNotNull('tgl_bayar')
            ->where('status', 'lunas') // Hanya yang sudah lunas
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulanPendapatan = array_fill(0, 12, 0);
        foreach ($perBulan as $item) {
            $bulanPendapatan[$item->bulan - 1] = (float) $item->total;
        }

        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return response()->json([
            'totalSPPT' => $totalSPPT,
            'totalNominal' => $totalNominal,
            'totalLunas' => $totalLunas,
            'totalBelumLunas' => $totalBelumLunas,
            'persenLunas' => $persenLunas,
            'persenBelumLunas' => $persenBelumLunas,
            'bulanLabels' => $bulanLabels,
            'bulanPendapatan' => $bulanPendapatan
        ]);
    }

    private function dashboardPetugas($userId)
    {
        $user = User::findOrFail($userId);
        $label = $user->label_petugas;

        // Ambil parameter tahun dari URL
        $tahun = request()->query('tahun'); // tanpa Request $request

        $currentPetugas = User::where('label_petugas', $label)->first();
        if (!$currentPetugas) {
            return view('dashboard')->with('error', 'Petugas tidak ditemukan');
        }

        $wajibPajakIds = WajibPajak::where('id_petugas', $currentPetugas->id)->pluck('id');

        // Filter PBB berdasarkan tahun jika ada
        $pbbQuery = Pbb::with('wajibPajak')->whereIn('no_wp', $wajibPajakIds);
        if ($tahun) {
            $pbbQuery->where('tahun', $tahun);
        }
        $pbbList = $pbbQuery->get();

        $totalSPPT = $pbbList->count();
        $totalpajak = $pbbList->sum('pajak_terhutang');
        $totalDharmaTirta = $pbbList->sum('dharma_tirta');
        $totalNominal = $totalpajak + $totalDharmaTirta;

        $totalLunas = $this->hitungTotalLunas($pbbList, $currentPetugas->id);
        $totalBelumLunas = $totalNominal - $totalLunas;

        $persenLunas = $totalNominal > 0 ? round(($totalLunas / $totalNominal) * 100, 2) : 0;
        $persenBelumLunas = 100 - $persenLunas;

        $totalKelompok = WajibPajak::where('id_petugas', $userId)
            ->whereNotNull('id_kelompok')
            ->select('id_kelompok')->distinct()->count();

        $tahunLabels = Pbb::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();

        return view('dashboard', compact(
            'totalSPPT',
            'totalNominal',
            'totalpajak',
            'totalLunas',
            'totalBelumLunas',
            'persenLunas',
            'persenBelumLunas',
            'totalKelompok',
            'tahunLabels',
            'tahun'
        ));
    }


    private function dashboardAdmin()
    {
        $totalSPPT = Pbb::count();
        $totalpajak = Pbb::sum('pajak_terhutang');
        $totalDharmaTirta = Pbb::sum('dharma_tirta');
        $totalNominal = $totalpajak + $totalDharmaTirta;

        $pbbAll = Pbb::with('wajibPajak')->get();

        $totalLunas = $this->hitungTotalLunas($pbbAll);
        $totalBelumLunas = $totalNominal - $totalLunas;

        $persenLunas = $totalNominal > 0 ? round(($totalLunas / $totalNominal) * 100, 2) : 0;
        $persenBelumLunas = 100 - $persenLunas;

        $tahunLabels = Pbb::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        $tahunTerbaru = $tahunLabels[0] ?? date('Y');

        // Perbaiki query untuk pendapatan bulanan - harus dari data yang benar-benar lunas
        $perBulan = FormulirTarikan::selectRaw('MONTH(tgl_bayar) as bulan, SUM(total) as total')
            ->whereYear('tgl_bayar', $tahunTerbaru)
            ->whereNotNull('tgl_bayar')
            ->where('status', 'lunas') // Tambahkan filter status lunas
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulanPendapatan = array_fill(0, 12, 0);
        foreach ($perBulan as $item) {
            $bulanPendapatan[$item->bulan - 1] = (float) $item->total;
        }

        $petugasList = User::where('role', 'Petugas')->get();
        $petugasLabels = [];
        $petugasData = [];

        foreach ($petugasList as $petugas) {
            $wpIds = WajibPajak::where('id_petugas', $petugas->id)->pluck('id');
            $jumlah = Pbb::whereIn('no_wp', $wpIds)->count();
            $petugasLabels[] = $petugas->nama;
            $petugasData[] = $jumlah;
        }

        return view('dashboard', compact(
            'totalSPPT',
            'totalNominal',
            'totalpajak',
            'totalLunas',
            'totalBelumLunas',
            'persenLunas',
            'persenBelumLunas',
            'tahunLabels',
            'bulanPendapatan',
            'petugasLabels',
            'petugasData'
        ));
    }

    private function dashboardBendahara()
    {
        $totalpajak = Pbb::sum('pajak_terhutang');
        $totalDharmaTirta = Pbb::sum('dharma_tirta');
        $totalNominal = $totalpajak + $totalDharmaTirta;

        $pbbAll = Pbb::with('wajibPajak')->get();
        $totalLunas = $this->hitungTotalLunas($pbbAll);
        $totalSetoran = SetoranPBB::sum('jumlah_setor');

        $persenTercapai = $totalNominal > 0 ? round(($totalLunas / $totalNominal) * 100, 2) : 0;

        $petugasList = User::where('role', 'Petugas')->get();
        $petugasLabels = [];
        $petugasSetoran = [];

        foreach ($petugasList as $petugas) {
            $kelompokIds = WajibPajak::where('id_petugas', $petugas->id)
                ->whereNotNull('id_kelompok')
                ->pluck('id_kelompok')
                ->unique();

            $setoranIds = FormulirTarikan::whereIn('id_kelompok', $kelompokIds)
                ->whereNotNull('id_setoran')
                ->pluck('id_setoran');

            $totalSetoranPetugas = SetoranPBB::whereIn('id', $setoranIds)->sum('jumlah_setor');

            $petugasLabels[] = $petugas->nama;
            $petugasSetoran[] = $totalSetoranPetugas;
        }

        $tahunLabels = Pbb::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();

        return view('dashboard', compact(
            'totalNominal',
            'totalLunas',
            'persenTercapai',
            'petugasLabels',
            'petugasSetoran',
            'tahunLabels'
        ));
    }

    private function hitungTotalLunas($pbbCollection, $onlyPetugasId = null)
    {
        $totalLunas = 0;

        foreach ($pbbCollection as $pbb) {
            $wp = $pbb->wajibPajak;
            $idKelompok = $wp->id_kelompok ?? null;
            $idPetugas = $wp->id_petugas ?? null;

            if ($onlyPetugasId && $idPetugas != $onlyPetugasId) {
                continue; // skip jika bukan WP dari petugas ini
            }

            if ($idKelompok) {
                $formulir = FormulirTarikan::where('id_kelompok', $idKelompok)
                    ->where('status', 'lunas')
                    ->first();

                if ($formulir) {
                    $totalLunas += ($pbb->pajak_terhutang ?? 0) + ($pbb->dharma_tirta ?? 0);
                }
            }
        }

        return $totalLunas;
    }

    public function getDataByTahun($tahun)
    {
        $data = Pbb::where('tahun', $tahun)->get();

        $totalSPPT = $data->count();
        $totalpajak = $data->sum('pajak_terhutang');
        $totalDharmaTirta = $data->sum('dharma_tirta');
        $totalNominal = $totalpajak + $totalDharmaTirta;

        $totalLunas = $this->hitungTotalLunas($data);
        $totalBelumLunas = $totalNominal - $totalLunas;

        $persenLunas = $totalNominal > 0 ? round(($totalLunas / $totalNominal) * 100, 2) : 0;
        $persenBelumLunas = 100 - $persenLunas;

        // Ambil data pendapatan per bulan untuk tahun yang dipilih
        $perBulan = Pbb::selectRaw('MONTH(created_at) as bulan, SUM(pajak_terhutang + dharma_tirta) as total')
            ->where('tahun', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Inisialisasi array untuk 12 bulan
        $bulanPendapatan = array_fill(0, 12, 0);

        // Isi data pendapatan per bulan
        foreach ($perBulan as $item) {
            $bulanPendapatan[$item->bulan - 1] = (float) $item->total;
        }

        return response()->json([
            'totalSPPT' => $totalSPPT,
            'totalNominal' => $totalNominal,
            'totalLunas' => $totalLunas,
            'totalBelumLunas' => $totalBelumLunas,
            'persenLunas' => $persenLunas,
            'persenBelumLunas' => $persenBelumLunas,
            'bulanLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'bulanPendapatan' => $bulanPendapatan,
        ]);
    }
}
