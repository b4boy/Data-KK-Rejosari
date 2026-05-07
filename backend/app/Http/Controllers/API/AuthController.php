<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        
        // Hardcoded untuk demo
        if ($credentials['username'] === 'pakdukuh' && $credentials['password'] === 'rejosari123') {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user' => [
                    'name' => 'Pak Dukuh',
                    'role' => 'admin'
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }
    
    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}