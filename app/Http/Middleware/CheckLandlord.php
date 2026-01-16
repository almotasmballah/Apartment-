<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLandlord
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role === 'landlord') {
            return $next($request);
        }

        return response()->json([
            'message' => 'عفواً، هذه الصلاحية للمؤجرين فقط (Landlords).'
        ], 403);
    }
}
