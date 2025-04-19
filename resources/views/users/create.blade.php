@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Tambah Pengguna</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
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
