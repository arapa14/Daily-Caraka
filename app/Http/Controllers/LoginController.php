<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (auth()->attempt($request->only('email', 'password'))){
            return redirect()->route('dashboard');
        }

        return redirect()->back();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
