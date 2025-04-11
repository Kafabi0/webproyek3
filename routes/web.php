<?php
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return view('welcome');
});

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

