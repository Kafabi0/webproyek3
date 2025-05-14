<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('produk', ProdukController::class);

Route::post('/pay', [MidtransController::class, 'pay']);
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
Route::get('/transactions', function (Request $request) {
    return Transaction::where('user_id', $request->user_id)->with('produk')->get();
});

