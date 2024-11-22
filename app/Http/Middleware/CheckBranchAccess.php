<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBranchAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $branchId = $request->route('branch');

        if ($user->isBranchRestricted() && !$user->canManageBranch($branchId)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        return $next($request);
    }
}
