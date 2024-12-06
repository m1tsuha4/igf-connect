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

    public function logout(Request $request){
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => 'true',
                'data' => [],
                'message' => 'Logout success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }

    public function user(Request $request){
        try {
            return response()->json([
                'success' => 'true',
                'data' => $request->user(),
                'message' => 'Data berhasil ditemukan'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => 'false',
                'data' => [],
                'message' => $th->getMessage()
            ]);
        }
    }
}
