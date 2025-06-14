<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
Route::get('/', function () {
    return view('landingpage');
})->name('landingpage');


//jwt-auth secret [pwUV4D6RRIYLnt3zH1q3ecdfWu0ndkiH59NgOTnRPXq3mMzJe7AGD7nsYgNisvMo] set successfully.

use App\Http\Controllers\ProdukController;

Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');


use App\Http\Controllers\AuthController;

Route::get('/admin/users', [AuthController::class, 'index'])->name('users.index');
Route::get('/admin/users/create', [AuthController::class, 'create'])->name('users.create');
Route::post('/admin/users', [AuthController::class, 'store'])->name('users.store');
Route::get('/admin/users/{user}/edit', [AuthController::class, 'edit'])->name('users.edit');
Route::put('/admin/users/{user}', [AuthController::class, 'update'])->name('users.update');
Route::delete('/admin/users/{user}', [AuthController::class, 'destroy'])->name('users.destroy');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
Route::get('/pesanan/export', [OrderController::class, 'export'])->name('pesanan.export');

Route::get('/rekap-penjualan', [ReportController::class, 'index'])->name('rekap-penjualan.index');

// routes/web.php
use App\Http\Controllers\StoreStatusController;

Route::get('/store-status', [StoreStatusController::class, 'index'])->name('store_status.index');
Route::post('/update-store-status', [DashboardController::class, 'updateStoreStatus'])->name('update_store_status');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/store-status', [StoreStatusController::class, 'index']);
    Route::put('/admin/store-status', [StoreStatusController::class, 'update']);
});
