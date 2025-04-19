@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="my-4 text-center display-6"><b>Data Pengguna</b></h1>
    <a href="{{ route('users.create') }}" class="btn btn-success mb-3">
        <i class="bi bi-person-plus"></i> Tambah Pengguna
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->username }}</td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin hapus user ini?')">
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
