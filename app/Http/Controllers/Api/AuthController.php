<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 422);
        }

        if ($user->status === 'Inactive') {
            return response()->json(['message' => 'Account Deactivated: Please contact your Administrator.'], 403);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json([
            'token' => $user->createToken('web')->plainTextToken,
            'user' => $user->toApi(),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:100',
        ]);

        // Public sign-ups are always Warehouse Staff.
        // An Administrator can change the role in User Management.
        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => 'Warehouse Staff',
            'status' => 'Active',
            'last_login_at' => now(),
        ]);

        return response()->json([
            'token' => $user->createToken('web')->plainTextToken,
            'user' => $user->toApi(),
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->toApi());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
