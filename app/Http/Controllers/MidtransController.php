<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Produk;
use App\Models\Transaction; // Pastikan model Transaction diimpor
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Untuk logging
use Throwable; // Untuk menangani semua jenis error/exception

class MidtransController extends Controller
{
    /**
     * Memproses permintaan pembayaran dari Flutter dan mengembalikan URL Snap.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pay(Request $request)
    {
        Log::info('--- Memulai Proses Pembayaran Midtrans (PAY) ---');
        Log::info('Request payload:', $request->all());

        // --- VALIDASI INPUT DARI FLUTTER ---
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'total_gross_amount' => 'required|integer|min:0',
            'item_details' => 'required|array|min:1',
            'item_details.*.id' => 'required|exists:produks,id',
            'item_details.*.price' => 'required|integer|min:0', // Ini akan diabaikan karena harga diambil dari DB
            'item_details.*.quantity' => 'required|integer|min:1',
            'item_details.*.name' => 'required|string|max:255', // Ini akan diabaikan karena nama diambil dari DB
        ]);

        if ($validator->fails()) {
            Log::warning('Validasi input pembayaran gagal.', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        // --- KONFIGURASI MIDTRANS ---
        // Pindahkan konfigurasi ke constructor atau method terpisah jika sering digunakan
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); // Gunakan env variable
        Config::$isSanitized = true;
        Config::$is3ds = true;

        Log::info('Konfigurasi Midtrans:', [
            'server_key_exists' => !empty(Config::$serverKey),
            'is_production' => Config::$isProduction
        ]);

        // --- AMBIL DATA PENGGUNA ---
        try {
            $user = User::findOrFail($validatedData['user_id']);
            Log::info('User ditemukan:', ['user_id' => $user->id, 'username' => $user->username]);
        } catch (Throwable $e) {
            Log::error('User tidak ditemukan saat pembuatan transaksi.', ['user_id' => $validatedData['user_id'], 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        $orderId = 'ORDER-' . uniqid(); // Pastikan order_id unik
        Log::info('Order ID dibuat:', ['order_id' => $orderId]);

        // --- HITUNG ULANG TOTAL GROSS AMOUNT DI BACKEND (KEAMANAN PENTING) ---
        $calculatedGrossAmount = 0;
        $midtransItemDetails = [];
        $firstProdukId = null; // Untuk menyimpan ID produk pertama

        foreach ($validatedData['item_details'] as $index => $item) {
            try {
                $produkDb = Produk::findOrFail($item['id']); // Ambil data produk dari DB untuk harga dan nama
                Log::info('Produk ditemukan di DB:', ['produk_id' => $produkDb->id, 'price' => $produkDb->price]);
            } catch (Throwable $e) {
                Log::error('Produk tidak ditemukan saat perhitungan keranjang.', ['produk_id' => $item['id'], 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json(['message' => 'Produk dengan ID ' . $item['id'] . ' tidak ditemukan.'], 404);
            }

            // Set firstProdukId jika ini item pertama (untuk kolom 'produk_id' di tabel 'transactions')
            if ($index === 0) {
                $firstProdukId = $produkDb->id;
            }

            // Gunakan harga dari database, bukan dari Flutter untuk keamanan.
            $calculatedGrossAmount += ((int) $produkDb->price * (int) $item['quantity']);

            $midtransItemDetails[] = [
                'id' => $produkDb->id,
                'price' => (int) $produkDb->price, // Gunakan harga dari DB
                'quantity' => (int) $item['quantity'],
                'name' => $produkDb->title, // Gunakan nama dari DB
            ];
        }

        // Opsional: Verifikasi total_gross_amount dari Flutter dengan yang dihitung backend
        if ($validatedData['total_gross_amount'] != $calculatedGrossAmount) {
            Log::warning('Total gross_amount dari Flutter tidak cocok dengan perhitungan backend.', [
                'flutter_amount' => $validatedData['total_gross_amount'],
                'backend_amount' => $calculatedGrossAmount,
                'order_id' => $orderId
            ]);
            // Untuk keamanan tinggi, Anda bisa mengembalikan error di sini
            // return response()->json(['message' => 'Total belanja tidak sesuai. Silakan coba lagi.'], 422);
        }

        // --- DATA PEMBAYARAN KE MIDTRANS ---
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $calculatedGrossAmount, // Gunakan total yang dihitung ulang
            ],
            'customer_details' => [
                'first_name' => $user->username,
                'email' => $user->email ?? $user->username . '@example.com', // Gunakan email asli jika ada, fallback ke dummy
                'phone' => $validatedData['phone_number'],
            ],
            'item_details' => $midtransItemDetails,
            // Anda bisa menambahkan 'callbacks' di sini untuk URL notifikasi sukses/gagal di frontend setelah pembayaran Midtrans.
            // Ini berbeda dengan notification URL (callback) yang di set di dashboard.
            // Ini untuk REDIREKSI user setelah dari halaman Midtrans.
            'callbacks' => [
                 'finish' => env('APP_URL') . '/payment-finish', // Sesuaikan dengan URL frontend Anda
                 'error' => env('APP_URL') . '/payment-error',   // Sesuaikan
                 'pending' => env('APP_URL') . '/payment-pending', // Sesuaikan
             ],
        ];

        Log::info('Parameter Midtrans Snap:', $params);

        try {
            $snapUrl = Snap::getSnapUrl($params);
            Log::info('Snap URL berhasil dibuat.', ['snap_url' => $snapUrl]);
        } catch (Throwable $e) {
            Log::error('Gagal mendapatkan Snap URL dari Midtrans: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => $params
            ]);
            return response()->json([
                'message' => 'Gagal mendapatkan URL pembayaran. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }

        // --- SIMPAN TRANSAKSI KE DB ---
        // Simpan transaksi di database dengan status 'pending' sebelum diarahkan ke Midtrans
        try {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'produk_id' => $firstProdukId, // Ingat batasan jika banyak produk
                'order_id' => $orderId,
                'status' => 'pending', // Status awal selalu pending
                'total_price' => $calculatedGrossAmount,
                'address' => $validatedData['address'],
                'phone_number' => $validatedData['phone_number'],
            ]);
            Log::info('Transaksi berhasil disimpan ke DB.', ['transaction_id' => $transaction->id, 'order_id' => $orderId]);
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan transaksi ke database:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'user_id' => $user->id,
                    'produk_id' => $firstProdukId,
                    'order_id' => $orderId,
                    'total_price' => $calculatedGrossAmount,
                ]
            ]);
            return response()->json(['message' => 'Gagal menyimpan transaksi ke database.'], 500);
        }

        return response()->json([
            'redirect_url' => $snapUrl,
            'order_id' => $orderId // Mungkin berguna untuk frontend
        ]);
    }

    /**
     * Menangani callback/notification dari Midtrans.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request)
    {
        Log::info('--- Midtrans Callback Diterima (NOTIFICATION) ---');
        Log::info('Request payload dari Midtrans:', $request->all());

        try {
            // Konfigurasi Midtrans untuk notifikasi
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); // Harus sama dengan di metode pay
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');

            // Membuat objek notifikasi. Objek ini akan memverifikasi signature key secara otomatis
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;
            $statusCode = $notification->status_code;
            $grossAmount = $notification->gross_amount; // Penting untuk verifikasi

            Log::info("Data Notifikasi Midtrans: Order ID: {$orderId}, Status: {$transactionStatus}, Fraud: {$fraudStatus}, Status Code: {$statusCode}, Gross Amount: {$grossAmount}");

            // Temukan transaksi di database Anda berdasarkan order_id
            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning('Callback: Transaksi tidak ditemukan di database.', ['order_id' => $orderId, 'notification_data' => $request->all()]);
                // Penting: Kembalikan 404 agar Midtrans tidak terus mencoba mengirim notifikasi yang tidak dikenal
                return response()->json(['message' => 'Order not found in application database'], 404);
            }

            // Log status transaksi saat ini sebelum diperbarui
            Log::info("Callback: Transaksi ditemukan di DB. Order ID: {$orderId}, Status sebelumnya: {$transaction->status}");

            // Logika update status transaksi
            // Perhatikan status yang dikirim Midtrans. `capture` dan `settlement` adalah status sukses.
            // Pastikan Anda membandingkan dengan status yang mungkin dari Midtrans.
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $transaction->status = 'challenge';
                    Log::warning("Callback: Status 'challenge' untuk Order ID: {$orderId}");
                } else if ($fraudStatus == 'accept') {
                    $transaction->status = 'settlement'; // 'success' atau 'settlement'
                    Log::info("Callback: Status 'settlement' (capture/accept) untuk Order ID: {$orderId}");
                }
            } else if ($transactionStatus == 'settlement') {
                $transaction->status = 'settlement'; // 'success' atau 'settlement'
                Log::info("Callback: Status 'settlement' untuk Order ID: {$orderId}");
            } else if ($transactionStatus == 'pending') {
                $transaction->status = 'pending';
                Log::info("Callback: Status 'pending' untuk Order ID: {$orderId}");
            } else if ($transactionStatus == 'deny') {
                $transaction->status = 'denied';
                Log::warning("Callback: Status 'denied' untuk Order ID: {$orderId}");
            } else if ($transactionStatus == 'expire') {
                $transaction->status = 'expired';
                Log::warning("Callback: Status 'expired' untuk Order ID: {$orderId}");
            } else if ($transactionStatus == 'cancel') {
                $transaction->status = 'cancelled';
                Log::warning("Callback: Status 'cancelled' untuk Order ID: {$orderId}");
            } else {
                $transaction->status = 'unknown_' . $transactionStatus; // Untuk status yang tidak terduga
                Log::warning("Callback: Status tidak dikenal dari Midtrans: {$transactionStatus} untuk Order ID: {$orderId}");
            }

            // Simpan perubahan status
            $transaction->save();
            Log::info("Callback: Status transaksi berhasil diperbarui. Order ID: {$orderId}, Status baru: {$transaction->status}");

            // Berikan respons 200 OK ke Midtrans agar notifikasi tidak dikirim ulang
            return response()->json(['message' => 'Callback received and processed successfully'], 200);

        } catch (Throwable $e) {
            // Tangani semua exception yang mungkin terjadi selama proses callback
            Log::error('Callback: Terjadi kesalahan saat memproses notifikasi Midtrans: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            // Penting: Kembalikan 500 agar Midtrans mencoba mengirim ulang notifikasi (jika memungkinkan)
            return response()->json(['message' => 'Internal Server Error during callback processing'], 500);
        }
    }
}