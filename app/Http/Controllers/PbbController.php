<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Pbb;
use Illuminate\Http\Request;
use App\Imports\WajibPajakImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;


class PbbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'Admin') {
            // Admin lihat semua
            $pbbList = Pbb::with('wajibPajak')->get();
        } else {
            // Petugas hanya lihat data wajib pajak miliknya
            // $pbbList = Pbb::whereHas('wajibPajak', function ($query) use ($user) {
            //     $query->where('id_petugas', $user->id);
            // })->with('wajibPajak')->get();

            $pbbList = Pbb::whereHas('wajibPajak', function ($query) use ($user) {
                $query->whereHas('petugas', function ($q) use ($user) {
                    $q->where('label_petugas', $user->label_petugas);
                });
            })
                ->with('wajibPajak')
                ->get();
        }

        return view('wajib_pajak.wajib_pajak', compact('pbbList'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('excel_file');

        try {
            $importer = new WajibPajakImport();
            Excel::import($importer, $file);

            $count = $importer->getImportedCount();

            if ($count === 0) {
                return redirect()->route('wajib.pajak')->with('error', 'Tidak ada data baru yang diimpor. Semua data sudah ada.');
            }

            return redirect()->route('wajib.pajak')->with('success', "$count data berhasil diimpor.");
        } catch (\Exception $e) {
            return redirect()->route('wajib.pajak')->with('error', 'Gagal mengimpor: ' . $e->getMessage());
        }
    }
}
