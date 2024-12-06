<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if(Auth::attempt($request->only('email', 'password'))){

                $user = Auth::user();
                $token = $user->createToken('MyApp')->plainTextToken;
                $data = [
                    'id' => $user->id,
                    'token' => $token,
                    'username' => $user->username
                ];
                
                return response()->json([
                    'success' => 'true',
                    'data' => $data,
                    'message' => 'Login success'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }
}
