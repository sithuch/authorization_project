<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function login(Request $request)
    {
        $request->email;
        $request->password;
        $validator = validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        
        if ($validator->fails()) {  // Error: Factory has no fails() method
            return response()->json(["status" => 400, 'error' => $validator->errors()], 400);
        } else {
            // login logic

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(["status" => 400, 'error' => 'Invalid credentials'], 400);
            } else {
                $user = $request->user();
                $accessToken = $user->createToken('access_token', ['*'])->accessToken;
                // Store a value with a custom key

                // session()->put('access_token', $accessToken);

                // dd(session()->get('access_token'));
                    return response()->json([
                        'access_token' => $accessToken,
                        'expires_in' => date('Y-m-d H:i:s',strtotime(Carbon::now()->addHour())),
                    ]);
               
            }
        }
    }

    public function refresh(Request $request)
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
    }
}
