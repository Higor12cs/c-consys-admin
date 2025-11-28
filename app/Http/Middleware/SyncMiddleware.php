<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (! $token || ! str_starts_with($token, 'Bearer ')) {
            return response()->json(['error' => 'Invalid authorization header'], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $customer = Customer::where('api_token', $token)->first();

        if (! $customer) {
            return response()->json(['error' => 'Invalid API token'], 401);
        }

        $request->merge(['customer_id' => $customer->id]);

        return $next($request);
    }
}
