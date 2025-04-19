@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="my-4 text-center display-6"><b>Daftar Produk</b></h1>

    <a href="{{ route('produk.create') }}" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Produk
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Gambar</th>
                <th>Nama Produk</th>
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
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('produk.edit', $item) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('produk.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus produk ini?')" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
