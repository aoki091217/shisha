<?php

namespace App\Http\Middleware;

use App\Services\RoleService;
use Auth;
use Closure;
use Illuminate\Http\Request;

class RoleCheckMiddleware
{
    public function __construct(
        private RoleService $roleService
    ) {
        $this->roleService = $roleService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {    
	$preset = $this->roleService->findRolePreset($role);

        if (!in_array(Auth::user()->role_id, $preset)) {
            return redirect()->back()->with('validate.role', 'アクセス権限がありません。');
        }

        return $next($request);
    }
}
