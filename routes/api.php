<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua route di sini otomatis mendapat prefix /api.
| Gunakan middleware 'tenant' untuk endpoint yang butuh tenant scoping.
| Gunakan middleware 'auth:sanctum' untuk endpoint yang butuh authentication.
|
*/

// ── Public (tanpa auth & tanpa tenant) ──
Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'message' => 'PBXCF API is running',
        'time'    => now()->toIso8601String(),
    ]);
});

// ── Authenticated user info ──
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ── Tenant-scoped routes (butuh header X-Tenant-ID) ──
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    // Campaign, Donation, Category, Withdrawal routes akan ditambahkan di sini
});
