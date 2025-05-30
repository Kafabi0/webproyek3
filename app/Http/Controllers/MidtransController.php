<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produk;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Notification; // Impor kelas Notification untuk callback
use Midtrans\Snap;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Impor Log untuk logging

class MidtransController extends Controller
{
    public function pay(Request $request)
    {
        // --- VALIDASI INPUT DARI FLUTTER ---
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'total_gross_amount' => 'required|integer|min:0',
            'item_details' => 'required|array|min:1',
            'item_details.*.id' => 'required|exists:produks,id',
            'item_details.*.price' => 'required|integer|min:0',
            'item_details.*.quantity' => 'required|integer|min:1',
            'item_details.*.name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Midtrans Pay Validation Failed', ['errors' => $validator->errors()->toArray(), 'request' => $request->all()]);
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        // --- KONFIGURASI MIDTRANS ---
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // true jika sudah live
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // --- AMBIL DATA PENGGUNA ---
        try {
            $user = User::findOrFail($validatedData['user_id']);
        } catch (\Exception $e) {
            Log::error('User not found for Midtrans transaction', ['user_id' => $validatedData['user_id'], 'error' => $e->getMessage()]);
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }
        
        $orderId = 'ORDER-' . uniqid();

        // --- HITUNG ULANG TOTAL GROSS AMOUNT DI BACKEND (KEAMANAN) ---
        $calculatedGrossAmount = 0;
        $midtransItemDetails = [];
        $firstProdukId = null; // Untuk menyimpan ID produk pertama

        foreach ($validatedData['item_details'] as $index => $item) { // Tambahkan $index
            try {
                $produkDb = Produk::findOrFail($item['id']); // Ambil data produk dari DB
            } catch (\Exception $e) {
                Log::error('Produk not found for item in cart', ['produk_id' => $item['id'], 'error' => $e->getMessage()]);
                return response()->json(['message' => 'Produk dengan ID ' . $item['id'] . ' tidak ditemukan.'], 404);
            }
            
            // Set firstProdukId jika ini item pertama
            if ($index === 0) {
                $firstProdukId = $produkDb->id;
            }

            // Gunakan harga dari database, bukan dari Flutter untuk keamanan.
            $calculatedGrossAmount += ((int) $produkDb->price * (int) $item['quantity']);
            
            $midtransItemDetails[] = [
                'id' => $produkDb->id,
                'price' => (int) $produkDb->price, // Gunakan harga dari DB
                'quantity' => (int) $item['quantity'],
                'name' => $produkDb->title,
            ];
        }

        // Opsional: Verifikasi total_gross_amount dari Flutter dengan yang dihitung backend
        if ($validatedData['total_gross_amount'] != $calculatedGrossAmount) {
            Log::warning('Flutter gross_amount mismatch with backend calculation', [
                'flutter_amount' => $validatedData['total_gross_amount'],
                'backend_amount' => $calculatedGrossAmount,
                'order_id' => $orderId
            ]);
            // Anda bisa memilih untuk mengembalikan error atau tetap menggunakan calculatedGrossAmount
            // Untuk keamanan, disarankan mengembalikan error jika tidak cocok.
            // return response()->json(['message' => 'Total belanja tidak sesuai. Silakan coba lagi.'], 422);
        }

        // --- DATA PEMBAYARAN KE MIDTRANS ---
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $calculatedGrossAmount, // Gunakan total yang dihitung ulang
            ],
            'customer_details' => [
                'first_name' => $user->username, // Menggunakan username
                'email' => $user->username . '@gmail.com', // KEMBALIKAN EMAIL DUMMY (Midtrans biasanya butuh)
                'phone' => $validatedData['phone_number'],
            ],
            'item_details' => $midtransItemDetails,
            // Anda bisa menambahkan 'callbacks' di sini untuk URL notifikasi sukses/gagal di frontend setelah pembayaran Midtrans.
             'callbacks' => [
                 'finish' => 'URL_FRONTEND_SETELAH_SUKSES_BAYAR', 
                 'error' => 'URL_FRONTEND_SETELAH_GAGAL_BAYAR',   
                 'pending' => 'URL_FRONTEND_SETELAH_PENDING_BAYAR', 
             ],
        ];

        try {
            $snapUrl = Snap::getSnapUrl($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap URL generation failed: ' . $e->getMessage(), ['params' => $params]);
            return response()->json([
                'message' => 'Failed to get snap URL',
                'error' => $e->getMessage()
            ], 500);
        }

        // --- SIMPAN TRANSAKSI KE DB ---
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'produk_id' => $firstProdukId, // ✅ Tambahkan ini! Mengambil ID produk dari item pertama
            'order_id' => $orderId,
            'status' => 'pending',
            'total_price' => $calculatedGrossAmount,
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
        ]);
        
        return response()->json([
            'redirect_url' => $snapUrl
        ]);
    }

    // ✅ FUNGSI CALLBACK LENGKAP
    public function callback(Request $request)
    {
        // Konfigurasi Midtrans untuk notifikasi
        Config::$isProduction = false; // Sesuaikan dengan isProduction di metode pay
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');

        // Buat objek notifikasi
        $notification = new Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;
        $statusCode = $notification->status_code; // Status code dari Midtrans
        $grossAmount = $notification->gross_amount; // Gross amount dari Midtrans

        Log::info("Midtrans callback received for order {$orderId}: Status {$transactionStatus}, Fraud {$fraudStatus}");

        // Temukan transaksi di database Anda
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            Log::warning('Midtrans callback order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Verifikasi signature key (opsional, Notification class sudah melakukannya secara internal)
        // Jika Anda ingin verifikasi manual, pastikan urutan parameter hash sesuai dokumentasi Midtrans
        // $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);
        // if ($hashed !== $notification->signature_key) {
        //     Log::warning('Midtrans callback invalid signature', ['request' => $request->all()]);
        //     return response()->json(['message' => 'Invalid signature'], 403);
        // }

        // Logika update status transaksi
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $transaction->status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $transaction->status = 'success'; // atau 'settlement'
            }
        } else if ($transactionStatus == 'settlement') {
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

        Log::info("Transaction {$orderId} updated to status: {$transaction->status}");

        // Berikan respons 200 OK ke Midtrans agar notifikasi tidak dikirim ulang
        return response()->json(['message' => 'Callback received and processed'], 200);
    }
}