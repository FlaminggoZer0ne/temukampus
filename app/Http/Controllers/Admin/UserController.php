<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount('laporan')
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->orderBy('nama')->get();

        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:100'],
            'nim_nip' => ['nullable', 'string', 'max:20'],
            'email'   => ['required', 'email', 'max:100', 'unique:users,email'],
            'no_hp'   => ['nullable', 'string', 'max:20'],
            'role'    => ['required', Rule::in(User::ROLES)],
            'password' => ['required', 'string', 'min:6'],
        ]);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:100'],
            'nim_nip' => ['nullable', 'string', 'max:20'],
            'email'   => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp'   => ['nullable', 'string', 'max:20'],
            'role'    => ['required', Rule::in(User::ROLES)],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        // Admin tidak boleh menurunkan role-nya sendiri (agar tidak terkunci).
        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak bisa mengubah role akun Anda sendiri.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User dihapus.');
    }
}
