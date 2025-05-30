<?php

namespace App\Http\Controllers;

use App\Models\Produk; // Pastikan Anda memiliki model Produk
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Diperlukan jika Anda akan mengelola upload file gambar

class ProdukController extends Controller
{
    // --- API Endpoints untuk Komunikasi dengan Flutter ---

    // Metode untuk mendapatkan semua produk (GET /api/produk)
    public function indexApi()
    {
        // Ambil semua produk dari database
        $produks = Produk::all();

        // Format data agar sesuai dengan ekspektasi Flutter
        // Menggunakan 'title', 'price', 'category', 'image' untuk konsistensi
        $formattedProduks = $produks->map(function($produk) {
            return [
                'id' => $produk->id_produk, // Pastikan ini adalah nama primary key di tabel DB Anda
                'title' => $produk->title, // Menggunakan 'title'
                'price' => $produk->price, // Menggunakan 'price'
                'image' => $produk->image, // Menggunakan 'image'
                'category' => $produk->category, // Menggunakan 'category'
            ];
        });

        // Kembalikan data dalam format JSON
        return response()->json([
            'message' => 'Produk berhasil diambil',
            'data' => $formattedProduks
        ], 200); // Status 200 OK
    }

    // Metode untuk menyimpan produk baru (POST /api/produk)
    public function storeApi(Request $request)
    {
        // Validasi data yang dikirim dari Flutter
        // Nama field di sini harus sesuai dengan yang dikirim dari Flutter (title, price, category, image)
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string|max:255', // Validasi category
            'image' => 'nullable|string|max:255', // Asumsi Flutter mengirim nama file gambar (string)
        ]);

        // Buat produk baru di database
        $produk = Produk::create([
            'title' => $request->title,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $request->image, // Simpan nama file gambar yang dikirim dari Flutter
        ]);

        // Kembalikan respons JSON dengan status 201 Created
        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $produk // Mengembalikan data produk yang baru dibuat
        ], 201);
    }

    // --- Metode-metode untuk Tampilan Web (Admin Panel) ---
    // Metode ini tetap dipertahankan seperti aslinya untuk admin panel web Anda.

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
            'image' => 'nullable|image', // Ini berarti Anda mengharapkan upload file
        ]);

        // Handle upload gambar jika ada file yang dikirim dari form web
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        Produk::create([
            'title' => $request->title,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $imagePath ? 'storage/' . $imagePath : null, // Simpan path lengkap untuk web
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
            // Hapus gambar lama jika ada
            if ($produk->image && Storage::disk('public')->exists(str_replace('storage/', '', $produk->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $produk->image));
            }
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
        // Hapus gambar terkait jika ada
        if ($produk->image && Storage::disk('public')->exists(str_replace('storage/', '', $produk->image))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $produk->image));
        }
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
