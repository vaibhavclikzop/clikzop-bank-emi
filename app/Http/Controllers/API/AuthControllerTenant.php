<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthControllerTenant extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 401);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        try {
            if ($user->role === 'super_admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only tenant allowed',
                ], 403);
            }
            $tokenResult = $user->createToken('admin_token');

            $tokenModel = $tokenResult->accessToken;
            $tokenModel->expires_at = now()->addHours(24);
            $tokenModel->save();

            $token = $tokenResult->plainTextToken;
            $user->remember_token = $token;

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Login Successfully',
                'token' => $token,
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'token' => '',
                'data' => [],
            ]);
        }
    }
}
