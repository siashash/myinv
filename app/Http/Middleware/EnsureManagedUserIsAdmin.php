<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagedUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $roleName = strtolower((string) $request->session()->get('managed_user_role_name', ''));

        if ($roleName === 'admin') {
            return $next($request);
        }

        $roleId = $request->session()->get('role_id');
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role && strtolower($role->role_name) === 'admin') {
                $request->session()->put('managed_user_role_name', $role->role_name);
                return $next($request);
            }
        }

        abort(403, 'Only admin user can access user management.');
    }
}
