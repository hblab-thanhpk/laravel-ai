<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Ensure authenticated admin user has required permission.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login.form');
        }

        $user = Auth::user();

        if (! $user instanceof User || ! $user->hasPermission($permission)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
