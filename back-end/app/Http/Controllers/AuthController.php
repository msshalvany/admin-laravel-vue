<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * ثبت‌نام کاربر جدید
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $input = $request->only('username', 'password');
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'کاربر با موفقیت ثبت‌نام شد',
            'token' => $token,
        ], 201);
    }

    /**
     * ورود کاربر
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('username', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'نام کاربری یا رمز عبور اشتباه است'], 401);
        }

        return response()->json([
            'message' => 'ورود با موفقیت انجام شد',
            'token' => $token,
        ], 200);
    }

    /**
     * خروج کاربر
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['message' => 'با موفقیت خارج شدید']);
    }

    /**
     * گرفتن اطلاعات کاربر احراز هویت‌شده
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        return response()->json(['user' => $user]);
    }
}
