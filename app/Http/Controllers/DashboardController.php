<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        if ($role === 'admin'){
            return view('admin.dashboard');
        } elseif ($role === 'reviewer') {
            return view('reviewer.dashboard');
        } else {
            return view('caraka.dashboard');
        }
    }
}
