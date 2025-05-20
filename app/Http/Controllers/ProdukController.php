<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::all();
        return view('produk.index', compact('produk'));
    }

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|in:aksesoris,makanan,kandang,kesehatan',
            'image' => 'nullable|image',
        ]);

        $imagePath = $request->file('image')?->store('images', 'public');

        Produk::create([
            'title' => $request->title,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $imagePath ? 'storage/' . $imagePath : null,
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        return view('produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|in:aksesoris,makanan,kandang,kesehatan',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $produk->image = 'storage/' . $imagePath;
        }

        $produk->title = $request->title;
        $produk->price = $request->price;
        $produk->category = $request->category;
        $produk->save();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
