<?php

namespace App\Http\Middleware;

use App\Models\Activity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $path = $request->path();
        $method = $request->method();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $requestData = $request->all();

        $response = $next($request);
        $status = $response->getStatusCode();

        Activity::create([
            'path'          => $path,
            'method'        => $method,
            'ip_address'    => $ipAddress,
            'user_agent'    => $userAgent,
            'request_data'  => json_encode($requestData),
            'status'        => $status,
            'user_id'       => $userId
        ]);

        return $response;
    }
}
