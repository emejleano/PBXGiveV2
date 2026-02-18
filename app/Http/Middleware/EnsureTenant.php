<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EnsureTenant
 *
 * Memastikan setiap request API memiliki header X-Tenant-ID yang valid.
 * Header ini dibutuhkan untuk multi-tenant scoping pada semua operasi data.
 *
 * Cara pakai:
 *   - Kirim header "X-Tenant-ID: {id}" di setiap request API
 *   - Superadmin bisa skip tenant check (opsional)
 */
class EnsureTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-ID');

        // Jika tidak ada header X-Tenant-ID
        if (! $tenantId) {
            return response()->json([
                'message' => 'Header X-Tenant-ID is required.',
            ], 400);
        }

        // Validasi tenant exists dan aktif
        $tenant = Tenant::where('id', $tenantId)->where('is_active', true)->first();

        if (! $tenant) {
            return response()->json([
                'message' => 'Invalid or inactive tenant.',
            ], 403);
        }

        // Simpan tenant ke request untuk digunakan di controller/service
        $request->merge(['current_tenant' => $tenant]);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
