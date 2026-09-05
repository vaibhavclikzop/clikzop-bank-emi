<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (! $user->tenant_id) {
            return response()->json([
                'status' => false,
                'message' => 'Tenant not assigned',
            ], 403);
        }

        $tenant = Tenant::where('id', $user->tenant_id)->first();

        if (! $tenant || $tenant->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Tenant inactive',
            ], 403);
        }

        if ($tenant->expiry_date && $tenant->expiry_date < now()) {
            return response()->json([
                'status' => false,
                'message' => 'Tenant expired',
            ], 403);
        }

        return $next($request);
    }
}
