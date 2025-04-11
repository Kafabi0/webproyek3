<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

public function index()
{
    $users = User::all();
    return view('users.index', compact('users'));
}

public function create()
{
    return view('users.create');
}

public function store(Request $request)
{
    $request->validate([
        'username' => 'required|string|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
}

public function edit(User $user)
{
    return view('users.edit', compact('user'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'username' => 'required|string|max:255|unique:users,username,' . $user->id,
    ]);

    $user->update([
        'username' => $request->username,
        'password' => $request->password ? Hash::make($request->password) : $user->password,
    ]);

    return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
}

// Hapus user
public function destroy(User $user)
{
    $user->delete();
    return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
}

}
