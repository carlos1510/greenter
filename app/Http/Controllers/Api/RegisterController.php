<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        //die("aqui");
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:125|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //die()

        //die("aqui");

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'data' =>$user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'status' => 201
        ]);
    }
}
