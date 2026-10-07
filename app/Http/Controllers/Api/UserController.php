<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLES = ['Administrator', 'Inventory Manager', 'Warehouse Staff'];
    private const STATUSES = ['Active', 'Inactive', 'Pending'];

    private function adminOnly(Request $request): void
    {
        abort_unless($request->user()->role === 'Administrator', 403, 'Only Administrators can manage user accounts.');
    }

    public function index(): JsonResponse
    {
        return response()->json(User::orderByDesc('id')->get()->map->toApi()->values());
    }

    public function store(Request $request): JsonResponse
    {
        $this->adminOnly($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:100',
            'role' => ['required', Rule::in(self::ROLES)],
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'status' => $data['status'],
        ]);

        return response()->json($user->toApi(), 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->adminOnly($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6|max:100',
            'role' => ['required', Rule::in(self::ROLES)],
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $user->name = trim($data['name']);
        $user->email = strtolower(trim($data['email']));
        $user->role = $data['role'];
        $user->status = $data['status'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json($user->toApi());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->adminOnly($request);

        abort_if($user->id === 1, 403, 'Primary root Administrator account cannot be deleted.');
        abort_if($user->id === $request->user()->id, 403, 'You cannot delete your own active session account.');

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
