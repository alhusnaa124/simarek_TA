<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PbbController;
use App\Http\Controllers\RekapController;

Route::get('/', function () {
    return view('auth.login');
});
Route::middleware(['auth', 'verified', 'role:Admin'])->group(function () {
    Route::resource('petugas', PetugasController::class);
    Route::resource('bendahara', BendaharaController::class);
    Route::post('wajib_pajak/import', [PbbController::class, 'import'])->name('wajib_pajak.import');
    Route::get('/distribusi/{no_wp}/edit', [DistribusiController::class, 'edit'])->name('distribusi.edit');
    Route::put('/distribusi/{no_wp}', [DistribusiController::class, 'update'])->name('distribusi.update');
});
Route::middleware(['auth', 'verified', 'role:Admin,Bendahara'])->group(function () {
    Route::get('/rekap', [RekapController::class, 'indexBendahara'])->name('rekap');
    Route::get('/rekap/cetak-pdf', [RekapController::class, 'cetakPdf'])->name('rekap.cetak-pdf');
});

Route::middleware(['auth', 'verified', 'role:Admin,Petugas'])->group(function () {
    Route::get('/wajib-pajak', [PbbController::class, 'index'])->name('wajib.pajak');

    Route::get('/distribusi', [DistribusiController::class, 'index'])->name('distribusi');
});


Route::middleware(['auth', 'verified', 'role:Petugas'])->group(function () {
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/', [PembayaranController::class, 'index'])->name('index');
        Route::get('/bayar', [PembayaranController::class, 'create'])->name('create');
        Route::get('/{id}/detail', [PembayaranController::class, 'detail'])->name('detail');
        Route::get('/{id}/cetak', [PembayaranController::class, 'cetak'])->name('cetak');
        Route::get('/{id}', [PembayaranController::class, 'show'])->name('show');
        Route::post('/{id}/bayar', [PembayaranController::class, 'store'])->name('store');
        Route::delete('/{id}/hapus', [PembayaranController::class, 'destroy'])->name('destroy');

        Route::get('distribusi/edit_kelompok/{no_wp}', [DistribusiController::class, 'editKelompok'])->name('distribusi.editKelompok');
        Route::put('distribusi/edit_kelompok/{no_wp}', [DistribusiController::class, 'updateKelompok'])->name('distribusi.updateKelompok');
    });

    // Route Formulir - Dikelompokkan dengan prefix
    Route::prefix('formulir')->name('formulir.')->group(function () {
        Route::get('/kelompok/{id}', [FormulirController::class, 'show'])->name('show');
        Route::get('/{id}/cetak', [FormulirController::class, 'cetak'])->name('cetak');
        Route::get('/{id}/export', [FormulirController::class, 'exportPdf'])->name('export');
    });
    // Route alternatif untuk kompatibilitas dengan kode lama
    Route::get('/formulir', [FormulirController::class, 'index'])->name('formulir');
    Route::post('/formulir/tambah', [FormulirController::class, 'store'])->name('formulir.store');
    Route::post('/config/update', [FormulirController::class, 'updateConfig'])->name('update.config');
    Route::get('/config/formulir', [FormulirController::class, 'edit'])->name('config.formulir');
    Route::post('/config/formulir', [FormulirController::class, 'update'])->name('config.formulir.update');


    Route::get('/setoran/tambah', [SetoranController::class, 'create'])->name('setoran.tambah');
    Route::post('/setoran/store', [SetoranController::class, 'store'])->name('setoran.store');

    Route::get('/rekapPetugas', [RekapController::class, 'index'])->name('rekapPetugas');
    Route::get('/rekap-petugas/export-rekap-per-bagian', [RekapController::class, 'exportRekapPerBagian'])->name('rekap.export.bagian');


    Route::post('/kelompok/tambah', [KelompokController::class, 'store'])->name('kelompok.tambah');
    Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');

    Route::resource('kelompok', KelompokController::class);
});

Route::middleware(['auth', 'verified', 'role:Admin,Petugas,Bendahara'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data-tahun/{tahun}', [DashboardController::class, 'getDataByTahun']);
    Route::put('/setoran/update/{id}', [SetoranController::class, 'update'])->name('setoran.update');
    Route::get('/setoran/edit/{id}', [SetoranController::class, 'edit'])->name('setoran.edit');
    Route::get('/setoran', [SetoranController::class, 'index'])->name('setoran');
    Route::get('/setoran/cetak/{id}', [SetoranController::class, 'cetak'])->name('setoran.cetak');
    Route::get('/setoran/detail/{id}', [SetoranController::class, 'show'])->name('setoran.detail');
    Route::get('/rekap/export-wp', [RekapController::class, 'exportWajibPajak'])->name('rekap.export-wp');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/profilemanagement', [AuthenticatedSessionController::class, "profile"])->name("profile");
Route::post('/profilemanagement', [AuthenticatedSessionController::class, "updateProfile"])->name("updateprofile");
Route::post('/profilemanagement/editPassword', [AuthenticatedSessionController::class, "updatePassword"])->name("updatepassword");


Route::get('/side', function () {
    return view('layouts.sidebar');
});

require __DIR__ . '/auth.php';
