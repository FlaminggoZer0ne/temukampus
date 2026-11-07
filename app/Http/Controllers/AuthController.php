<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:100'],
            'nim_nip' => ['required', 'string', 'max:20'],
            'email'   => ['required', 'email', 'max:100', 'unique:users,email'],
            'no_hp'   => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'nama'     => $data['nama'],
            'nim_nip'  => $data['nim_nip'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'mahasiswa', // registrasi mandiri hanya untuk mahasiswa
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }

    public function profil()
    {
        return view('auth.profil', ['user' => auth()->user()]);
    }

    public function updateProfil(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:100'],
            'nim_nip' => ['nullable', 'string', 'max:20'],
            'email'   => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp'   => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
