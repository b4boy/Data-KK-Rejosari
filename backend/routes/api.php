<?php

use App\Http\Controllers\API\KartuKeluargaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});

// Login route
Route::post('/login', function(Request $request) {
    $username = $request->username;
    $password = $request->password;
    
    if ($username === 'pakdukuh' && $password === 'rejosari2024') {
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => ['name' => 'Pak Dukuh']
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Login gagal'
    ], 401);
});

// CRUD Kartu Keluarga
Route::get('/dashboard/stats', [KartuKeluargaController::class, 'stats']);
Route::get('/kk', [KartuKeluargaController::class, 'index']);
Route::get('/kk/{id}', [KartuKeluargaController::class, 'show']);
Route::post('/kk', [KartuKeluargaController::class, 'store']);
Route::put('/kk/{id}', [KartuKeluargaController::class, 'update']);
Route::delete('/kk/{id}', [KartuKeluargaController::class, 'destroy']);