@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Produk</h2>
    <a href="{{ route('produk.create') }}" class="btn btn-primary mb-3">Tambah Produk</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produk as $item)
                <tr>
                    <td>
                        @if($item->image)
                            <img src="{{ asset($item->image) }}" width="80">
                        @else
                            Tidak ada
                        @endif
                    </td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->price }}</td>
                    <td>
                        <a href="{{ route('produk.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('produk.destroy', $item) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus produk ini?')" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
