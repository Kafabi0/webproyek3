<?php

namespace App\Http\Controllers;

use App\Models\StoreStatus;
use Illuminate\Http\Request;

class StoreStatusController extends Controller
{
    /**
     * Get the current store status (API endpoint)
     */
    public function getStatus()
    {
        // Retrieve or create the store status
        $storeStatus = StoreStatus::firstOrCreate([], ['is_open' => true]);

        return response()->json([
            'status' => $storeStatus->is_open ? 'buka' : 'tutup',
            'is_open' => $storeStatus->is_open
        ]);
    }

    /**
     * Update the store status (API endpoint)
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:buka,tutup'
        ]);

        // Retrieve or create the store status
        $storeStatus = StoreStatus::firstOrCreate([], ['is_open' => true]);

        // Update the status
        $storeStatus->is_open = $request->status === 'buka';
        $storeStatus->save();

        return response()->json([
            'success' => true,
            'message' => 'Status toko berhasil diperbarui',
            'status' => $storeStatus->is_open ? 'buka' : 'tutup'
        ]);
    }

    /**
     * Display the store status management page (for web)
     */
    public function index()
    {
        $status = StoreStatus::firstOrCreate([], ['is_open' => true]);
        return view('admin.store-status', compact('status'));
    }

    /**
     * Update the store status (for web form)
     */
    public function update(Request $request)
    {
        $request->validate([
            'is_open' => 'required|boolean'
        ]);

        $status = StoreStatus::firstOrCreate([], ['is_open' => true]);
        $status->is_open = $request->is_open;
        $status->save();

        return back()->with('success', 'Status toko berhasil diperbarui');
    }
}
