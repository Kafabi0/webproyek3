@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Edit Pengguna</h2>

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400" value="{{ $user->username }}" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (opsional)</label>
                <input type="password" name="password" id="password" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>

            <div class="flex justify-between">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
