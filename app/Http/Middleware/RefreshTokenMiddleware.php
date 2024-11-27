<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $request = Request::create('oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => env("CLIENT_ID"),
            'client_secret' => env("CLIENT_SECRET"),
            'scope' => '*',
        ]);
        $result = app()->handle($request);
        $response = json_decode($result->getContent(), true); 
        // dd($response->getstatusCode());
        if ($result->getstatusCode()) {
            $tokens = json_decode($result->getContent(), true);
            return response()->json([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => date('Y-m-d h:i:s',strtotime(Carbon::now()->addSeconds($tokens['expires_in']))),
            ]);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return $next($request);
    }
}
