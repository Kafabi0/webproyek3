@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Produk</h2>
    <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="title" value="{{ $produk->title }}" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Harga</label>
            <input type="text" name="price" value="{{ $produk->price }}" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Gambar Baru (opsional)</label>
            <input type="file" name="image" class="form-control">
            <br>
            @if($produk->image)
                <img src="{{ asset($produk->image) }}" width="100">
            @endif
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
