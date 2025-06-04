<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Untuk DB::raw()

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan tanggal mulai dan akhir dari request, atau null jika tidak ada
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;

        $transactionsQuery = Transaction::query();

        // Terapkan filter tanggal pada query utama
        if ($startDate && $endDate) {
            $transactionsQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $transactionsQuery->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $transactionsQuery->whereDate('created_at', '<=', $endDate);
        }
        
        // Query untuk total penjualan & jumlah transaksi (hanya status 'settlement')
        $settledTransactionsQuery = (clone $transactionsQuery)->where('status', 'settlement');

        $totalSales = $settledTransactionsQuery->sum('total_price');
        $transactionCount = $settledTransactionsQuery->count();
        $averageOrderValue = $transactionCount > 0 ? $totalSales / $transactionCount : 0;
        $settlementCount = $settledTransactionsQuery->count(); // Akan sama dengan transactionCount jika hanya menghitung settled

        // Penjualan per Status (untuk tabel breakdown)
        $salesByStatus = $transactionsQuery->select('status', DB::raw('count(*) as count'), DB::raw('sum(total_price) as total'))
                                         ->groupBy('status')
                                         ->get()
                                         ->keyBy('status')
                                         ->toArray();

        $formattedSalesByStatus = [];
        foreach ($salesByStatus as $status => $data) {
            $formattedSalesByStatus[$status] = [
                'count' => $data['count'],
                'total' => $data['total']
            ];
        }

        // Ambil data untuk grafik
        $salesChartData = $this->getSalesChartData($startDate, $endDate);

        return view('rekap-penjualan.index', compact(
            'totalSales',
            'transactionCount',
            'averageOrderValue',
            'settlementCount',
            'formattedSalesByStatus',
            'salesChartData' // Kirim data grafik ke view
        ));
    }

    /**
     * Mengambil data penjualan harian untuk grafik.
     * Hanya menghitung transaksi dengan status 'settlement'.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    protected function getSalesChartData(?Carbon $startDate, ?Carbon $endDate): array
    {
        $data = Transaction::query()
                           ->where('status', 'settlement');

        if ($startDate && $endDate) {
            $data->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $data->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $data->whereDate('created_at', '<=', $endDate);
        }

        $salesByDay = $data->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total_price')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $labels = $salesByDay->pluck('date')->map(function($date){
            return Carbon::parse($date)->format('d M Y');
        });
        $values = $salesByDay->pluck('total_price');

        return ['labels' => $labels, 'values' => $values];
    }
}