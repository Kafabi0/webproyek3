<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produk; // Pastikan model Produk diimpor
use App\Models\Transaction; // Pastikan model Transaction diimpor
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Validator; // Impor Validator

class MidtransController extends Controller
{
    public function pay(Request $request)
    {
        // --- MODIFIKASI DI SINI ---
        // ✅ Validasi input dari Flutter, termasuk alamat dan nomor telepon
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'produk_id' => 'required|exists:produks,id',
            'address' => 'required|string|max:255', // Tambahkan validasi untuk alamat
            'phone_number' => 'required|string|max:20', // Tambahkan validasi untuk nomor telepon
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422); // Gunakan status code 422 untuk error validasi
        }

        // Ambil data yang sudah divalidasi
        $validatedData = $validator->validated();

        // ✅ Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // true jika sudah live
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // ✅ Ambil data user & produk berdasarkan ID yang divalidasi
        $user = User::findOrFail($validatedData['user_id']);
        $produk = Produk::findOrFail($validatedData['produk_id']);
        $orderId = 'ORDER-' . uniqid();

        // ✅ Data pembayaran ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $produk->price, // Gunakan harga dari produk yang diambil dari DB
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                // --- MODIFIKASI DI SINI ---
                'address' => $validatedData['address'], // Tambahkan alamat ke detail pelanggan
                'phone' => $validatedData['phone_number'], // Tambahkan nomor telepon ke detail pelanggan (field Midtrans biasanya 'phone')
                // --- AKHIR MODIFIKASI ---
            ],
            'item_details' => [[
                'id' => $produk->id, // Gunakan ID dari produk yang diambil dari DB
                'price' => (int) $produk->price, // Gunakan harga dari produk yang diambil dari DB
                'quantity' => 1, // Asumsi kuantitas 1 untuk checkout per item
                'name' => $produk->title, // Gunakan nama dari produk yang diambil dari DB
            ]]
            // Jika Anda mengimplementasikan checkout banyak item, struktur item_details akan berbeda
        ];
        // --- AKHIR MODIFIKASI ---


        // ✅ Generate Snap URL
        try {
            $snapUrl = Snap::getSnapUrl($params);
        } catch (\Exception $e) {
            // Log error Midtrans API untuk debugging
            \Log::error('Midtrans Snap URL generation failed: ' . $e->getMessage(), ['params' => $params]);
            return response()->json([
                'message' => 'Failed to get snap URL',
                'error' => $e->getMessage()
            ], 500);
        }

        // ✅ Simpan transaksi ke DB
        // --- MODIFIKASI DI SINI ---
        Transaction::create([
            'user_id' => $user->id,
            'produk_id' => $produk->id,
            'order_id' => $orderId,
            'status' => 'pending',
            'total_price' => $produk->price,
            // Jika Anda memiliki kolom untuk alamat dan telepon di tabel transactions, simpan di sini
            // 'address' => $validatedData['address'],
            // 'phone_number' => $validatedData['phone_number'],
        ]);
        // --- AKHIR MODIFIKASI ---


        // ✅ Kembalikan JSON ke Flutter
        return response()->json([
            'redirect_url' => $snapUrl
        ]);
    }

    // Metode callback tetap sama seperti sebelumnya
    public function callback(Request $request)
    {
        // ✅ Verifikasi signature Midtrans
        $serverKey = env('MIDTRANS_SERVER_KEY');
        // Pastikan urutan parameter hash sesuai dengan dokumentasi Midtrans
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            \Log::warning('Midtrans callback invalid signature', ['request' => $request->all()]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // ✅ Temukan transaksi berdasarkan order_id
        $transaction = Transaction::where('order_id', $request->order_id)->first();

        if (!$transaction) {
             \Log::warning('Midtrans callback order not found', ['order_id' => $request->order_id]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // ✅ Update status transaksi
        // Ambil status dari notifikasi Midtrans
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        \Log::info("Midtrans callback received for order {$request->order_id}: Status {$transactionStatus}, Fraud {$fraudStatus}");


        // Contoh logika update status berdasarkan dokumentasi Midtrans
        if ($transactionStatus == 'capture') {
            // untuk transaksi via kartu kredit dengan 3DS, status awal `pending` -> `authorize` -> `capture`
            if ($fraudStatus == 'challenge') {
                $transaction->status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $transaction->status = 'success'; // atau 'settlement'
            }
        } else if ($transactionStatus == 'settlement') {
            // untuk transaksi non-kartu kredit
            $transaction->status = 'success'; // atau 'settlement'
        } else if ($transactionStatus == 'pending') {
             $transaction->status = 'pending';
        } else if ($transactionStatus == 'deny') {
             $transaction->status = 'denied';
        } else if ($transactionStatus == 'expire') {
             $transaction->status = 'expired';
        } else if ($transactionStatus == 'cancel') {
             $transaction->status = 'cancelled';
        }

        $transaction->save();

        // Berikan respons 200 OK ke Midtrans agar notifikasi tidak dikirim ulang
        return response()->json(['message' => 'Callback received and processed'], 200);
    }
}
