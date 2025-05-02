@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="my-4 text-center display-6"><b>Data Pengguna</b></h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Username</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->username }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
