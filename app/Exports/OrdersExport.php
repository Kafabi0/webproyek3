<?php

namespace App\Exports;

use App\Models\Transaction; // Sesuaikan dengan model transaksi Anda
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon; // Untuk memformat tanggal/waktu

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orderId;
    protected $startDate;
    protected $endDate;

    public function __construct($orderId, $startDate, $endDate)
    {
        $this->orderId = $orderId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $transactions = Transaction::query();

        // Terapkan filter yang sama seperti di metode index controller Anda
        if ($this->orderId) {
            $transactions->where('order_id', 'like', '%' . $this->orderId . '%');
        }

        if ($this->startDate && $this->endDate) {
            // Pastikan format tanggal/waktu sesuai dengan kolom created_at di database
            $transactions->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        } elseif ($this->startDate) {
            $transactions->whereDate('created_at', '>=', $this->startDate);
        } elseif ($this->endDate) {
            $transactions->whereDate('created_at', '<=', $this->endDate);
        }
        
        // Eager load relasi 'user' jika 'username' diambil dari tabel user
        $transactions->with('user'); 

        // Urutkan data jika diperlukan
        $transactions->orderBy('created_at', 'desc');

        return $transactions->get();
    }

    /**
     * Menentukan header kolom di file Excel
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal & Waktu',
            'Order ID',
            'Jenis Transaksi',
            'Channel',
            'Status',
            'Nilai (Rp)',
            'Nama Pelanggan',
            'No Telepon',
            'Alamat',
        ];
    }

    /**
     * Memetakan setiap baris data dari koleksi ke format yang sesuai untuk Excel
     * @param mixed $transaction
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->created_at->format('d M Y H:i'),
            $transaction->order_id,
            'Pembayaran', // Ini statis berdasarkan template Blade Anda
            'Online Payment', // Ini statis berdasarkan template Blade Anda
            ucfirst($transaction->status),
            number_format($transaction->total_price, 0, ',', '.'), // Format nilai Rupiah
            $transaction->user ? $transaction->user->username : '-', // Ambil username dari relasi user
            $transaction->phone_number ?? '-', // Menggunakan null coalescing operator untuk default '-'
            $transaction->address ?? '-', // Menggunakan null coalescing operator untuk default '-'
        ];
    }
}