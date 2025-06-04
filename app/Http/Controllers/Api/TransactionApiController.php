<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TransactionApiController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('user')->latest()->get();

        $data = $transactions->map(function($transaction) {
            // >>> UBAH DARI $transaction->gross_amount MENJADI $transaction->total_price <<<
            return [
                'id' => $transaction->id,
                'order_id' => $transaction->order_id,
                'total_price' => (double) $transaction->total_price, // <<< PERBAIKAN DI SINI
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->toIso8601String(),
                'phone_number' => $transaction->phone_number ?? '-',
                'address' => $transaction->address ?? '-',
                'user_name' => $transaction->user ? $transaction->user->username : 'Guest',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil diambil.',
            'data' => $data,
        ], 200);
    }

    public function show($order_id)
    {
        $transaction = Transaction::with('user')->where('order_id', $order_id)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi berhasil diambil.',
            'data' => [
                'id' => $transaction->id,
                'order_id' => $transaction->order_id,
                // >>> UBAH DARI $transaction->gross_amount MENJADI $transaction->total_price <<<
                'total_price' => (double) $transaction->total_price, // <<< PERBAIKAN DI SINI
                'status' => $transaction->status,
                'created_at' => $transaction->created_at->toIso8601String(),
                'phone_number' => $transaction->phone_number ?? '-',
                'address' => $transaction->address ?? '-',
                'user_name' => $transaction->user ? $transaction->user->username : 'Guest',
            ],
        ], 200);
    }
}