<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthenticationController extends Controller
{
    public function register(RegisterRequest $request){
    $request->validated();
    $userData = [
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        ];
        $user = User::create($userData);
        $token = $user->createToken('forumapp')->plainTextToken;
        return response([
        'user' => $user,
        'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request){
        $request->validated();
        $user = User::whereUsername($request->username)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response([
                "message" => "Invalid username or password"
            ],422);
        }

        $token = $user->createToken('forumapp')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
            ], 200);

    }
    
}
