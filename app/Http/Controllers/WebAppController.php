<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class WebAppController extends Controller
{
    //
    public function login()
    {
        return view('login');
    }

    public function setSession(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi email dan password tidak valid',
            ], 404);
        }
        $user = Auth::user();
        Auth::login($user);
        $respon_data = [
            'message' => 'Proses login selesai dilakukan',
        ];
        return response()->json($respon_data, 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function session()
    {
        dd(auth()->user());
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function pesan()
    {
        return view('wa_pesan');
    }

    public function pegawai()
    {
        return view('pegawai');
    }

    public function kirim($id)
    {
        $data['id'] = $id;
        return view('kirim2', $data);
    }
}
