<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Pastikan ini mengacu pada Model Transaction Anda
use Illuminate\Http\Request;
use Carbon\Carbon;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query();

        // Filter berdasarkan Order ID
        if ($request->has('order_id') && !empty($request->order_id)) {
            $query->where('order_id', 'like', '%' . $request->order_id . '%');
        }

        // Filter berdasarkan rentang tanggal menggunakan created_at
        if ($request->has('start_date') && !empty($request->start_date) && $request->has('end_date') && !empty($request->end_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Default: Tampilkan transaksi bulan ini jika tidak ada filter tanggal
            $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        // Eager load relasi user untuk menghindari N+1 query problem saat mengambil email
        $transactions = $query->with('user')->orderBy('created_at', 'desc')->paginate(10);

        return view('pesanan', compact('transactions'));
    }
}