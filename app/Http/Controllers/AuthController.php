<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $role = auth()->user()->role;
            return $role === 'admin' 
                ? redirect('/admin/dashboard') 
                : redirect('/user/dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $role = auth()->user()->role;
            return $role === 'admin'
                ? redirect()->intended('/admin/dashboard')
                : redirect()->intended('/user/dashboard');
        }

        return back()->withErrors(['username' => 'Invalid credentials'])->onlyInput('username');
    }

    public function showRegister()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $role = auth()->user()->role;
            return $role === 'admin' 
                ? redirect('/admin/dashboard') 
                : redirect('/user/dashboard');
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'username' => ['required','string','max:50','unique:users,username'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        Auth::login($user);
        return $user->role === 'admin' ? redirect('/admin/dashboard') : redirect('/user/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}


