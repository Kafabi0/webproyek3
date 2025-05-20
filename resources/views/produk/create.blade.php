@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Tambah Produk</h2>

        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text" name="title" id="title" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="text" name="price" id="price" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category" id="category" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    <option value="aksesoris">Aksesoris</option>
                    <option value="makanan">Makanan</option>
                    <option value="kandang">Kandang</option>
                    <option value="kesehatan">Kesehatan</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
                <input type="file" name="image" id="image" class="form-control w-full text-sm text-gray-500">
            </div>

            <div class="flex justify-between">
                <a href="{{ url()->previous() }}" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
