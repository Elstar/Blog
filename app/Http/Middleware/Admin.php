<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->type != 'admin') {
            return response()->json(['success' => false, 'errors' => 'You dont have permission']);
        }
        return $next($request);
    }
}
