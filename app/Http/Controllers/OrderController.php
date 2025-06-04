<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Sesuaikan dengan model transaksi Anda
use App\Exports\OrdersExport; // Impor kelas ekspor Anda
use Maatwebsite\Excel\Facades\Excel; // Impor facade Excel
use Illuminate\Http\Request;
use Carbon\Carbon; // Untuk memformat tanggal/waktu

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan dengan filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $transactions = Transaction::query();

        if ($request->filled('order_id')) {
            $transactions->where('order_id', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Pastikan Anda memparsing tanggal agar mencakup seluruh hari
            $transactions->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } elseif ($request->filled('start_date')) {
            $transactions->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $transactions->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $transactions->orderBy('created_at', 'desc')->paginate(10); // Sesuaikan angka paginasi jika diperlukan

        return view('pesanan.index', compact('transactions'));
    }

    /**
     * Mengunduh data pesanan ke file Excel berdasarkan filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $orderId = $request->input('order_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Unduh file Excel menggunakan kelas export yang sudah dibuat
        return Excel::download(
            new OrdersExport($orderId, $startDate, $endDate),
            'data_pesanan_' . Carbon::now()->format('Ymd_His') . '.xlsx'
        );
    }
}