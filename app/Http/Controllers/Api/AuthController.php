<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\BaseController;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|integer|unique:users',
            'password' => 'required|string|min:6',
        ]);
        try {
            $user = User::create([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);
            $user->is_pin_set = false;
            $token = $user->createToken('api_token')->plainTextToken;
            $data = [
                'user'  => $user,
                'token' => $token,
            ];
            return $this->sendResponse($data, 'User registered successfully');
        } catch (\Exception $e) {
            return $this->sendError('User registration failed', [$e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);
        try {
            $user = User::where('phone', $request->phone)->first();
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            unset($user->wallet_balance);
            $user->is_pin_set = $user->pin ? true : false;
            $token = $user->createToken('api_token')->plainTextToken;
            $data = [
                'user'  => $user,
                'token' => $token,
            ];
            return $this->sendResponse($data, 'User logged in successfully');
        } catch (\Exception $e) {
            return $this->sendError('Login failed', [$e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->sendResponse([], 'User logged out successfully');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed', [$e->getMessage()]);
        }
    }
}
