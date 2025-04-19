@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Edit Produk</h2>

        <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text" name="title" id="title" value="{{ $produk->title }}" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="text" name="price" id="price" value="{{ $produk->price }}" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar Baru (opsional)</label>
                <input type="file" name="image" id="image" class="form-control w-full text-sm text-gray-500">
                @if($produk->image)
                    <div class="mt-3">
                        <p class="text-sm text-gray-600">Gambar saat ini:</p>
                        <img src="{{ asset($produk->image) }}" alt="Produk Image" class="w-32 h-auto rounded border">
                    </div>
                @endif
            </div>

            <div class="flex justify-between">
                <a href="{{ url()->previous() }}" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                    <i class="bi bi-pencil-square"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
