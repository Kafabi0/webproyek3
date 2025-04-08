<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::all()->map(function ($item) {
            $item->image = $item->image
                ? url($item->image)
                : 'https://via.placeholder.com/150'; // fallback jika kosong
            return $item;
        });

        return response()->json([
            'data' => $produk
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|string',
            'image' => 'nullable|string',
        ]);

        $produk = Produk::create([
            'title' => $request->title,
            'price' => $request->price,
            'image' => $request->image,
        ]);

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'data' => $produk
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        $produk->image = $produk->image
        ? url($produk->image)
        : 'https://via.placeholder.com/150';

    return response()->json([
        'data' => $produk
    ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|string',
            'image' => 'nullable|string',
        ]);

        $produk->update([
            'title' => $request->title,
            'price' => $request->price,
            'image' => $request->image,
        ]);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'data' => $produk
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
