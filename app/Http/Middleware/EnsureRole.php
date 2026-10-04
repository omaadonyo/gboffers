<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureRole {
    public function handle(Request $request, Closure $next, string ...$roles) {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->is_suspended) abort(403, 'Account suspended.');
        if (!in_array($user->role, $roles, true)) abort(403, 'Insufficient permissions.');
        return $next($request);
    }
}
