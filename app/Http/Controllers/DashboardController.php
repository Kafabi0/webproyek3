<?php

namespace App\Http\Controllers;

use App\Models\StoreStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch the current store status
        $storeStatus = StoreStatus::first();
        if (!$storeStatus) {
            // Create a default store status if none exists
            $storeStatus = StoreStatus::create(['is_open' => true]);
        }

        $data = [
            'total_pesanan' => 2000,
            'pesanan_tertunda' => 100,
            'pesanan_selesai' => 5000,
            'pesanan_dibatalkan' => 500,

            'harian' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data' => [200, 150, 300, 100, 400, 250, 200]
            ],

            'bulanan' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
                'data' => [400, 300, 500, 450, 600, 700, 500]
            ],

            'top_users' => [
                ['name' => 'Chidi Nurseni', 'total' => 10],
                ['name' => 'Kafidul Aulis', 'total' => 5],
                ['name' => 'M. Naufal', 'total' => 3],
            ],

            'top_products' => [
                ['name' => 'Kalung Pita', 'total' => 100],
                ['name' => 'Aks. Kandang', 'total' => 80],
                ['name' => 'obat antikutu', 'total' => 60],
            ],

            'store_status' => $storeStatus // Include the store status in the data
        ];

        return view('dashboard', compact('data'));
    }
    public function updateStoreStatus(Request $request)
    {
        // Validate the request
        $request->validate([
            'is_open' => 'required|boolean',
        ]);

        // Fetch the current store status
        $storeStatus = StoreStatus::first();
        if (!$storeStatus) {
            // Create a default store status if none exists
            $storeStatus = StoreStatus::create(['is_open' => true]);
        }

        // Update the store status
        $storeStatus->is_open = $request->is_open;
        $storeStatus->save();

        return back()->with('success', 'Status toko telah diperbarui.');
    }
}
