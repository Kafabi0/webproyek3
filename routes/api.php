<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\StoreStatusController;
use App\Http\Controllers\Api\TransactionApiController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/transactions', [TransactionApiController::class, 'index']);
Route::get('/transactions/{order_id}', [TransactionApiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('produk', ProdukController::class);

Route::post('/pay', [MidtransController::class, 'pay']);
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
//Route::get('/transactions', function (Request $request) {
   // return Transaction::where('user_id', $request->user_id)->with('produk')->get();
//});
Route::get('/store-status', [StoreStatusController::class, 'getStatus']);
Route::post('/store-status', [StoreStatusController::class, 'updateStatus']);
